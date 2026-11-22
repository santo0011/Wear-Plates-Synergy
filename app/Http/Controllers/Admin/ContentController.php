<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use App\Services\ImageUploader;
use App\Support\Cms;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Generic editor for the website page sections registered in config/cms.php.
 */
class ContentController extends Controller
{
    public function __construct(private ImageUploader $uploader) {}

    public function edit(string $page, string $section): View
    {
        $def = $this->definition($page, $section);
        $row = PageSection::firstOrNew(['page' => $page, 'section' => $section]);

        return view('admin.content.edit', [
            'page' => $page,
            'sectionKey' => $section,
            'pageDef' => Cms::page($page),
            'def' => $def,
            'content' => $row->content ?? [],
            'isActive' => $row->exists ? $row->is_active : true,
        ]);
    }

    public function update(Request $request, string $page, string $section): RedirectResponse
    {
        $def = $this->definition($page, $section);
        $row = PageSection::firstOrNew(['page' => $page, 'section' => $section]);
        $old = $row->content ?? [];

        $this->validateInput($request, $def['fields']);

        $content = $this->collect(
            (array) $request->input('content', []),
            (array) $request->file('content', []),
            $def['fields'],
            $old
        );

        // Delete uploaded images that are no longer used by this section.
        foreach (array_diff(PageSection::imagePaths($old), PageSection::imagePaths($content)) as $orphan) {
            $this->uploader->delete($orphan);
        }

        $row->content = $content;
        $row->is_active = $request->boolean('is_active');
        $row->save();

        return redirect()->route('admin.content.edit', [$page, $section])
            ->with('success', $def['label'].' saved.');
    }

    private function definition(string $page, string $section): array
    {
        $def = Cms::section($page, $section);
        abort_unless($def, 404);

        return $def;
    }

    private function validateInput(Request $request, array $fields): void
    {
        $rules = [];
        $names = [];
        foreach ($fields as $key => $field) {
            if ($field['type'] === 'repeater') {
                foreach ($field['fields'] as $subKey => $sub) {
                    $rules["content.$key.*.$subKey"] = $this->rule($sub['type']);
                    $names["content.$key.*.$subKey"] = $field['label'].' → '.$sub['label'];
                }
            } else {
                $rules["content.$key"] = $this->rule($field['type']);
                $names["content.$key"] = $field['label'];
            }
        }

        // Uploaded files live in a separate bag; merge them so the "image" rules see them.
        $data = array_replace_recursive($request->all(), $request->allFiles());
        $validator = Validator::make($data, $rules, [], $names);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function rule(string $type): array
    {
        return match ($type) {
            'image' => ImageUploader::RULES,
            'boolean' => ['nullable', 'boolean'],
            'text', 'icon', 'url' => ['nullable', 'string', 'max:500'],
            default => ['nullable', 'string', 'max:30000'],
        };
    }

    /** Build the content array for $fields from request input, keeping existing images. */
    private function collect(array $input, array $files, array $fields, array $old): array
    {
        $out = [];
        foreach ($fields as $key => $field) {
            $type = $field['type'];

            if ($type === 'repeater') {
                $items = [];
                $rows = is_array($input[$key] ?? null) ? $input[$key] : [];
                $rowFiles = is_array($files[$key] ?? null) ? $files[$key] : [];
                foreach ($rows as $uid => $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $item = $this->collect($row, is_array($rowFiles[$uid] ?? null) ? $rowFiles[$uid] : [], $field['fields'], []);
                    if (array_filter($item, fn ($v) => $v !== null && $v !== '' && $v !== false)) {
                        $items[] = $item;
                    }
                }
                $out[$key] = $items;

                continue;
            }

            if ($type === 'image') {
                $current = $input[$key.'__current'] ?? ($old[$key] ?? null);
                $current = (is_string($current) && $current !== '') ? $current : null;
                $file = $files[$key] ?? null;
                if ($file instanceof UploadedFile) {
                    $out[$key] = $this->uploader->store($file, 'content');
                } elseif (! empty($input[$key.'__remove'])) {
                    $out[$key] = null;
                } else {
                    $out[$key] = $current;
                }

                continue;
            }

            $value = $input[$key] ?? null;
            if ($type === 'boolean') {
                $out[$key] = ! empty($value);
            } else {
                $out[$key] = is_string($value) ? trim(str_replace(["\r\n", "\r"], "\n", $value)) : null;
            }
        }

        return $out;
    }
}
