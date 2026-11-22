<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Shared behaviour for catalogue models: unique slugs, image URLs, active/ordered scopes.
 */
trait HasSlugAndImage
{
    public static function bootHasSlugAndImage(): void
    {
        static::saving(function ($model) {
            if (blank($model->slug)) {
                $model->slug = $model->name;
            }
            $model->slug = static::uniqueSlug(Str::slug($model->slug), $model->getKey());
        });
    }

    public static function uniqueSlug(string $base, $ignoreId = null): string
    {
        $base = $base !== '' ? $base : Str::random(8);
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /** Public URL of the stored image (uploads/... or bundled images/...), or null. */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset($this->image) : null;
    }
}
