<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class Setting extends Model
{
    public const CACHE_KEY = 'site_settings';

    public const GROUPS = [
        'general' => ['label' => 'General & Logos', 'icon' => 'fa-solid fa-sliders'],
        'contact' => ['label' => 'Contact Details', 'icon' => 'fa-solid fa-address-book'],
        'delivery' => ['label' => 'Delivery & Logistics', 'icon' => 'fa-solid fa-truck-fast'],
        'seo' => ['label' => 'SEO', 'icon' => 'fa-solid fa-magnifying-glass'],
        'social' => ['label' => 'Social Links', 'icon' => 'fa-solid fa-share-nodes'],
        'mail' => ['label' => 'Email / SMTP', 'icon' => 'fa-solid fa-envelope-open-text'],
    ];

    /** Choices for settings of type "select". */
    public const OPTIONS = [
        'smtp_encryption' => ['tls' => 'TLS / STARTTLS (port 587)', 'ssl' => 'SSL (port 465)', 'none' => 'None (port 25)'],
    ];

    protected $fillable = ['group', 'key', 'label', 'type', 'value', 'help', 'sort_order'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /** All settings as [key => value], cached until a setting changes. */
    public static function allValues(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, function () {
                if (! Schema::hasTable('settings')) {
                    return [];
                }

                return static::query()->pluck('value', 'key')->all();
            });
        } catch (Throwable) {
            return [];
        }
    }

    public static function getValue(string $key, $default = null)
    {
        $value = static::allValues()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    /**
     * Parse a "list" setting: one item per line, fields separated by "|".
     * Returns arrays of trimmed fields.
     */
    public static function listValue(string $key): array
    {
        $raw = (string) static::getValue($key, '');

        return collect(preg_split('/\r\n|\r|\n/', $raw))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->map(fn ($line) => array_map('trim', explode('|', $line)))
            ->values()
            ->all();
    }
}
