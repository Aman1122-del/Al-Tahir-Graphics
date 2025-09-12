<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'price_display', // Keep the display price for backward compatibility
        'image_url',
        'image_path',
        'category',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the local public image path for this service.
     * Always resolve to /images/<filename>, falling back to a default image if needed.
     */
    public function getImagePathAttribute(): string
    {
        // If stored image path is available, use it via storage helper
        if (!empty($this->attributes['image_path'])) {
            return asset('storage/' . ltrim($this->attributes['image_path'], '/'));
        }

        $raw = (string) ($this->image_url ?? '');

        // Determine filename from various possible inputs
        $filename = '';
        if ($raw !== '') {
            if (str_starts_with($raw, '/images')) {
                $filename = basename($raw);
            } elseif (!preg_match('#^https?://#i', $raw)) {
                $filename = basename($raw);
            } else {
                $filename = basename(parse_url($raw, PHP_URL_PATH) ?? '');
            }
        }

        if ($filename === '') {
            $filename = 'logo.jpg';
        }

        // URL-encode filename to handle spaces and special characters
        $encodedFilename = rawurlencode($filename);

        // Return full asset URL
        return asset('images/' . $encodedFilename);
    }

    /**
     * Scope a query to only include active services.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured services.
     */
    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    /**
     * Scope a query to order services by sort order.
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('title');
    }

    /**
     * Get the formatted price for display
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'PKR ' . number_format($this->price, 0);
    }

    public function samples(): HasMany
    {
        return $this->hasMany(ServiceSample::class)->orderBy('sort_order')->limit(10);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
