<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Throwable;

/**
 * Applies the SMTP settings saved in Admin → Website Settings → Email / SMTP
 * to Laravel's mail configuration at runtime. When no SMTP host is saved,
 * the .env mail configuration is left untouched.
 */
class MailSettings
{
    public static function apply(): void
    {
        $s = Setting::allValues();
        $host = trim((string) ($s['smtp_host'] ?? ''));

        if ($host !== '') {
            $encryption = $s['smtp_encryption'] ?? 'tls';

            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $host,
                'mail.mailers.smtp.port' => (int) ($s['smtp_port'] ?? 587) ?: 587,
                // "smtps" = implicit TLS (465); "smtp" upgrades with STARTTLS automatically when offered.
                'mail.mailers.smtp.scheme' => $encryption === 'ssl' ? 'smtps' : 'smtp',
                'mail.mailers.smtp.username' => ($s['smtp_username'] ?? null) ?: null,
                'mail.mailers.smtp.password' => self::password($s['smtp_password'] ?? null),
                'mail.mailers.smtp.timeout' => 15,
            ]);
        }

        if (! empty($s['mail_from_address'])) {
            config(['mail.from.address' => $s['mail_from_address']]);
        }
        if (! empty($s['mail_from_name'])) {
            config(['mail.from.name' => $s['mail_from_name']]);
        }
    }

    public static function isConfigured(): bool
    {
        return trim((string) Setting::getValue('smtp_host', '')) !== '';
    }

    private static function password(?string $encrypted): ?string
    {
        if (! $encrypted) {
            return null;
        }
        try {
            return Crypt::decryptString($encrypted);
        } catch (Throwable) {
            return null; // APP_KEY changed or value corrupted – re-enter the password in the admin panel
        }
    }
}
