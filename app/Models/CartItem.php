<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'service_id',
        'service_sample_id',
        'design_id',
        'quantity',
        'unit_price',
        'custom_requirements',
        'design_preview_path',
        'wedding_details',
        'visiting_card_details',
        'panaflex_details',
        'flyer_brochure_details',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'wedding_details' => 'array',
        'visiting_card_details' => 'array',
        'panaflex_details' => 'array',
        'flyer_brochure_details' => 'array',
    ];

    /**
     * Get the service that owns the cart item.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function sample(): BelongsTo
    {
        return $this->belongsTo(ServiceSample::class, 'service_sample_id');
    }

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    /**
     * Get the user that owns the cart item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate the total price for this cart item.
     */
    public function getTotalPriceAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Get formatted total price.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return 'PKR ' . number_format($this->total_price, 0);
    }
}
