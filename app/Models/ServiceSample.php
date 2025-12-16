<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ServiceSample extends Model
{
    protected $fillable = [
        'service_id', 'title', 'slug', 'description',
        'unit_price', 'price_display', 'image_path',
        'sub_category', 'sample_type', 'is_active', 'sort_order',
    ];

    // ✅ Frontend ko data bhejne ke liye zaroori line
    protected $appends = ['formatted_price', 'full_image_url'];

    // ✅ Naya accessor
    public function getFullImageUrlAttribute(): string
    {
        return $this->getImagePathAttribute();
    }

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
     * ✅ MAIN LOGIC YAHAN HAI
     */
    public function getImagePathAttribute(): string
    {
        // DB se value uthao
        $imagePathValue = $this->attributes['image_path'] ?? null;

        // Agar DB khali hai to default logo dikhao
        if (empty($imagePathValue)) {
            return asset('images/logo.jpg');
        }

        // Logic: asset() function hamesha 'public' folder se start karta hai.

        // Case 1: Agar DB mein path already 'images/' se shuru ho raha hai
        // Example: 'images/wedding-cards/royal-gold-foil-wedding-card.jpg'
        if (str_starts_with($imagePathValue, 'images/')) {
            return asset($imagePathValue);
            // Result: http://localhost/images/wedding-cards/... (Correct!)
        }

        // Case 2: Agar sirf filename hai
        // Example: 'Wedding Card sample 02.jpg'
        // To hum shuru mein 'images/' khud laga denge.
        return asset('images/' . ltrim($imagePathValue, '/'));
        // Result: http://localhost/images/Wedding Card sample 02.jpg (Correct!)
    }

    public function getFormattedPriceAttribute(): string
    {
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
                $sample->slug = Str::slug($sample->title);
            }
        });
        static::updating(function (ServiceSample $sample) {
            if (empty($sample->slug)) {
                $sample->slug = Str::slug($sample->title);
            }
        });
    }
}
