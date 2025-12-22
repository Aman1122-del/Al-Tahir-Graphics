<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ReturnRequest extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'user_id',
        'type',
        'reason',
        'description',
        'status',
        'admin_notes',
        'refund_amount',
        'refund_method',
        'tracking_number',
        'images',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'images' => 'array',
        'refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the order that owns the return request.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the order item that owns the return request.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the user that owns the return request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted refund amount.
     */
    public function getFormattedRefundAmountAttribute(): string
    {
        return $this->refund_amount ? 'PKR ' . number_format($this->refund_amount, 0) : 'N/A';
    }

    /**
     * Check if request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if request is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if request is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'completed' => 'success',
            'refunded' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get status text.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            'refunded' => 'Refunded',
            default => 'Unknown',
        };
    }

    /**
     * Get reason text.
     */
    public function getReasonTextAttribute(): string
    {
        return match($this->reason) {
            'defective_product' => 'Defective Product',
            'wrong_item' => 'Wrong Item Received',
            'not_as_described' => 'Not as Described',
            'changed_mind' => 'Changed Mind',
            'late_delivery' => 'Late Delivery',
            'other' => 'Other',
            default => 'Unknown',
        };
    }

    /**
     * Get type text.
     */
    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'return' => 'Return',
            'cancellation' => 'Cancellation',
            default => 'Unknown',
        };
    }

    // Scopes
    public function scopeByStatus(Builder $query, $status): void
    {
        $query->where('status', $status);
    }

    public function scopeByType(Builder $query, $type): void
    {
        $query->where('type', $type);
    }

    public function scopeByUser(Builder $query, $userId): void
    {
        $query->where('user_id', $userId);
    }

    public function scopePending(Builder $query): void
    {
        $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): void
    {
        $query->where('status', 'approved');
    }

    public function scopeRejected(Builder $query): void
    {
        $query->where('status', 'rejected');
    }
}
