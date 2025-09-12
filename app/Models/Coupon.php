<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)
              ->where('valid_from', '<=', Carbon::today())
              ->where('valid_until', '>=', Carbon::today());
    }

    public function scopeValid(Builder $query): void
    {
        $query->active()
              ->where(function ($q) {
                  $q->whereNull('max_uses')
                    ->orWhere('used_count', '<', 'max_uses');
              });
    }

    public function isValid(): bool
    {
        return $this->is_active &&
               Carbon::today()->between($this->valid_from, $this->valid_until) &&
               ($this->max_uses === null || $this->used_count < $this->max_uses);
    }

    public function calculateDiscount($orderAmount): float
    {
        if ($orderAmount < $this->min_order_amount) {
            return 0;
        }

        if ($this->type === 'percentage') {
            return ($orderAmount * $this->value) / 100;
        }

        return $this->value;
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }
}
