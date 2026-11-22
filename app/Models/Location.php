<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public const TYPES = [
        'corporate' => 'Corporate Office',
        'plant' => 'Manufacturing Plant',
        'regional' => 'Regional Office',
    ];

    /** Bounding box used by the stylised India map (equirectangular projection). */
    public const MAP_MIN_LON = 68.0;

    public const MAP_MAX_LAT = 37.5;

    public const MAP_SCALE = 20;

    protected $fillable = ['city', 'state', 'type', 'address', 'phone', 'latitude', 'longitude', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw("CASE type WHEN 'corporate' THEN 0 WHEN 'plant' THEN 1 ELSE 2 END")->orderBy('sort_order')->orderBy('city');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function getHasMapPointAttribute(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function getMapXAttribute(): float
    {
        return round(($this->longitude - self::MAP_MIN_LON) * self::MAP_SCALE, 1);
    }

    public function getMapYAttribute(): float
    {
        return round((self::MAP_MAX_LAT - $this->latitude) * self::MAP_SCALE, 1);
    }
}
