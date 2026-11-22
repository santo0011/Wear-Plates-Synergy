<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores admin-uploaded images under public/uploads (the "uploads" disk) so they are
 * web-accessible without a storage symlink. Paths are saved relative to /public.
 */
class ImageUploader
{
    public const RULES = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144', 'dimensions:max_width=8000,max_height=8000'];

    public function store(UploadedFile $file, string $folder): string
    {
        $name = Str::uuid()->toString().'.'.strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        Storage::disk('uploads')->putFileAs($folder, $file, $name);

        return 'uploads/'.trim($folder, '/').'/'.$name;
    }

    /** Replace an existing image; returns the new relative path. */
    public function replace(?string $oldPath, UploadedFile $file, string $folder): string
    {
        $new = $this->store($file, $folder);
        $this->delete($oldPath);

        return $new;
    }

    /** Only ever deletes files we uploaded (never bundled artwork in /public/images). */
    public function delete(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/') && ! str_contains($path, '..')) {
            Storage::disk('uploads')->delete(substr($path, strlen('uploads/')));
        }
    }
}
