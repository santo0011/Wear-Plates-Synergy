<?php

namespace App\Models;

use App\Models\Concerns\HasSlugAndImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use HasSlugAndImage;

    protected $fillable = [
        'name', 'slug', 'short_description', 'description', 'icon', 'image',
        'sort_order', 'is_active', 'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts(): HasMany
    {
        return $this->products()->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    public function getIconClassAttribute(): string
    {
        return $this->icon ?: 'fa-solid fa-industry';
    }
}
