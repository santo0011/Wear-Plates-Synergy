<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * One editable block of a website page (e.g. "home.hero"). Fields are defined in config/cms.php
 * and stored as JSON in `content`.
 */
class PageSection extends Model
{
    public const CACHE_KEY = 'page_sections';

    protected $fillable = ['page', 'section', 'content', 'is_active'];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /** All sections as ["page.section" => ['content' => [...], 'active' => bool]], cached. */
    public static function allContent(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, function () {
                if (! Schema::hasTable('page_sections')) {
                    return [];
                }

                return static::query()->get()->mapWithKeys(fn (self $s) => [
                    $s->page.'.'.$s->section => ['content' => $s->content ?? [], 'active' => $s->is_active],
                ])->all();
            });
        } catch (Throwable) {
            return [];
        }
    }

    /** Every uploaded image path referenced by a content array (recursively). */
    public static function imagePaths(array $content): array
    {
        $paths = [];
        array_walk_recursive($content, function ($value) use (&$paths) {
            if (is_string($value) && str_starts_with($value, 'uploads/')) {
                $paths[] = $value;
            }
        });

        return array_values(array_unique($paths));
    }
}
