<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_method',
        'subtotal',
        'shipping_cost',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'payment_screenshot_path',
        'account_details_sent',
        'admin_notes',
        'paid_at',
        'assigned_designer_id',
        'design_status',
        'design_due_date',
        'design_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'account_details_sent' => 'boolean',
        'paid_at' => 'datetime',
        'design_due_date' => 'date',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the assigned designer for the order.
     */
    public function assignedDesigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_designer_id');
    }

    /**
     * Get the order notes for the order.
     */
    public function orderNotes(): HasMany
    {
        return $this->hasMany(OrderNote::class);
    }

    /**
     * Get the invoice for the order.
     */
    public function invoice(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return 'PKR ' . number_format($this->total_amount, 0);
    }

    /**
     * Get formatted subtotal.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'PKR ' . number_format($this->subtotal, 0);
    }

    /**
     * Get formatted shipping cost.
     */
    public function getFormattedShippingCostAttribute(): string
    {
        return 'PKR ' . number_format($this->shipping_cost, 0);
    }

    /**
     * Check if order is paid.
     */
    public function isPaid(): bool
    {
        return in_array($this->payment_status, ['paid', 'pending_verification']);
    }

    /**
     * Check if order is pending payment verification.
     */
    public function isPendingVerification(): bool
    {
        return $this->payment_status === 'pending_verification';
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ATG';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return $prefix . $date . $random;
    }

    // Scopes for filtering
    public function scopeByStatus(Builder $query, $status): void
    {
        $query->where('order_status', $status);
    }

    public function scopeByDesignStatus(Builder $query, $status): void
    {
        $query->where('design_status', $status);
    }

    public function scopeByPaymentStatus(Builder $query, $status): void
    {
        $query->where('payment_status', $status);
    }

    public function scopeByDateRange(Builder $query, $startDate, $endDate): void
    {
        $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeAssignedToDesigner(Builder $query, $designerId): void
    {
        $query->where('assigned_designer_id', $designerId);
    }

    // Helper methods
    public function canBeAssigned(): bool
    {
        return in_array($this->design_status, ['pending', 'in_progress']);
    }

    public function isDesignCompleted(): bool
    {
        return $this->design_status === 'completed';
    }

    public function isDesignInProgress(): bool
    {
        return $this->design_status === 'in_progress';
    }
}
