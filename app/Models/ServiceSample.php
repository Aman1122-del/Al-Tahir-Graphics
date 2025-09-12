<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ServiceSample extends Model
{
    protected $fillable = [
        'service_id',
        'title',
        'slug',
        'description',
        'unit_price',
        'price_display',
        'image_path',
        'sub_category',
        'sample_type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the local public image path for this sample.
     */
    public function getImagePathAttribute(): string
    {
        // If stored image path is available, use it via storage helper
        if (!empty($this->attributes['image_path'])) {
            return asset('storage/' . ltrim($this->attributes['image_path'], '/'));
        }

        // Fallback to a default image
        return asset('images/logo.jpg');
    }

    /**
     * Get the formatted price for display
     */
    public function getFormattedPriceAttribute(): string
    {
        // Use price_display if available, otherwise format unit_price
        if ($this->price_display) {
            return $this->price_display;
        }
        
        return $this->unit_price ? 'PKR ' . number_format($this->unit_price, 0) : 'Price on request';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (ServiceSample $sample) {
            if (empty($sample->slug)) {
                $sample->slug = static::generateUniqueSlug($sample->title);
            }
        });

        static::updating(function (ServiceSample $sample) {
            // If slug is empty, regenerate
            if (empty($sample->slug)) {
                $sample->slug = static::generateUniqueSlug($sample->title, $sample->id);
            }
        });
    }

    private static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'sample';
        $slug = $base;
        $suffix = 2;
        while (static::query()
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }
        return $slug;
    }
}


