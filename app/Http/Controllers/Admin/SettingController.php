<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Support\MailSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private ImageUploader $uploader) {}

    public function edit(string $group = 'general'): View
    {
        abort_unless(array_key_exists($group, Setting::GROUPS), 404);

        return view('admin.settings.edit', [
            'group' => $group,
            'groups' => Setting::GROUPS,
            'settings' => Setting::where('group', $group)->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, string $group): RedirectResponse
    {
        abort_unless(array_key_exists($group, Setting::GROUPS), 404);

        $settings = Setting::where('group', $group)->get()->keyBy('key');

        $rules = [];
        foreach ($settings as $key => $setting) {
            $rules["settings.$key"] = match ($setting->type) {
                'image' => ImageUploader::RULES,
                'email' => ['nullable', 'email', 'max:160'],
                'url' => ['nullable', 'url', 'max:500'],
                'tel' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s\-()]*$/'],
                'boolean' => ['nullable', 'boolean'],
                'textarea', 'list' => ['nullable', 'string', 'max:10000'],
                'embed' => ['nullable', 'url', 'max:2000', 'starts_with:https://www.google.com/maps/embed'],
                'select' => ['nullable', Rule::in(array_keys(Setting::OPTIONS[$key] ?? []))],
                'password' => ['nullable', 'string', 'max:500'],
                default => ['nullable', 'string', 'max:500'],
            };
        }
        if ($settings->has('smtp_port')) {
            $rules['settings.smtp_port'] = ['nullable', 'integer', 'between:1,65535'];
        }
        if ($settings->has('smtp_host')) {
            $rules['settings.smtp_host'] = ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9.\-]+$/'];
        }

        $request->validate($rules, [
            'settings.*.starts_with' => 'Paste the "src" URL from Google Maps → Share → Embed a map (it starts with https://www.google.com/maps/embed).',
        ], $settings->mapWithKeys(fn ($s, $k) => ["settings.$k" => $s->label])->all());

        DB::transaction(fn () => $this->saveValues($request, $settings));

        Cache::forget(Setting::CACHE_KEY);

        return redirect()->route('admin.settings.edit', $group)->with('success', Setting::GROUPS[$group]['label'].' settings saved.');
    }

    private function saveValues(Request $request, $settings): void
    {
        foreach ($settings as $key => $setting) {
            if ($setting->type === 'image') {
                if ($request->hasFile("settings.$key")) {
                    $setting->value = $this->uploader->replace($setting->value, $request->file("settings.$key"), 'settings');
                } elseif ($request->boolean("remove.$key")) {
                    $this->uploader->delete($setting->value);
                    $setting->value = null;
                }
            } elseif ($setting->type === 'boolean') {
                $setting->value = $request->boolean("settings.$key") ? '1' : '0';
            } elseif ($setting->type === 'password') {
                // Never echoed back to the browser; blank input keeps the saved password.
                $value = (string) $request->input("settings.$key", '');
                if ($request->boolean("clear.$key")) {
                    $setting->value = null;
                } elseif ($value !== '') {
                    $setting->value = Crypt::encryptString($value);
                }
            } else {
                $value = $request->input("settings.$key");
                $setting->value = is_string($value) ? trim($value) : $value;
            }
            $setting->save();
        }
    }

    /** Send a test email using the saved SMTP settings. */
    public function testMail(Request $request): RedirectResponse
    {
        $data = $request->validate(['test_email' => ['required', 'email', 'max:160']]);

        if (! MailSettings::isConfigured()) {
            return back()->with('error', 'Save the SMTP host and login details first, then send a test email.');
        }

        // Re-apply in case settings changed during this request.
        Cache::forget(Setting::CACHE_KEY);
        MailSettings::apply();

        try {
            $site = setting('site_name', config('app.name'));
            Mail::raw(
                "This is a test email from the {$site} admin panel.\n\nIf you received this, your SMTP settings are working correctly.",
                fn ($m) => $m->to($data['test_email'])->subject("SMTP test – {$site}")
            );
        } catch (Throwable $e) {
            Log::warning('SMTP test failed: '.$e->getMessage());

            return back()->withInput()->with('error', 'Test email failed: '.Str::limit($e->getMessage(), 220));
        }

        return back()->with('success', "Test email sent to {$data['test_email']}. Check the inbox (and spam folder).");
    }
}
