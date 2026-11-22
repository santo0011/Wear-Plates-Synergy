<?php

namespace App\Models;

use App\Models\Concerns\HasSlugAndImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasSlugAndImage;

    protected $fillable = [
        'product_category_id', 'name', 'subtitle', 'slug', 'short_description', 'description', 'image',
        'highlights', 'specifications', 'data_table', 'data_table_title', 'applications',
        'sort_order', 'is_active', 'is_featured', 'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /** Falls back to the category artwork when the product has no image of its own. */
    public function getDisplayImageUrlAttribute(): ?string
    {
        return $this->image_url ?? $this->category?->image_url;
    }

    public function getUrlAttribute(): string
    {
        return route('products.show', [$this->category, $this]);
    }
}
