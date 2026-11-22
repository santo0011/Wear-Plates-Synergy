<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enquiry extends Model
{
    public const STATUSES = [
        'new' => ['label' => 'New', 'badge' => 'danger'],
        'in_progress' => ['label' => 'In Progress', 'badge' => 'warning'],
        'quoted' => ['label' => 'Quoted', 'badge' => 'info'],
        'closed' => ['label' => 'Closed', 'badge' => 'success'],
        'spam' => ['label' => 'Spam', 'badge' => 'secondary'],
    ];

    protected $fillable = [
        'product_id', 'name', 'company_name', 'mobile', 'email', 'subject', 'message',
        'status', 'admin_notes', 'ip_address', 'user_agent', 'read_at',
    ];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function getStatusBadgeAttribute(): string
    {
        return self::STATUSES[$this->status]['badge'] ?? 'secondary';
    }
}
