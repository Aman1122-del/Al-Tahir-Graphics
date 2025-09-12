<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Quote extends Model
{
    protected $fillable = [
        'quote_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'project_description',
        'requirements',
        'estimated_price',
        'status',
        'assigned_designer_id',
        'valid_until',
        'admin_notes',
        'designer_notes',
    ];

    protected $casts = [
        'estimated_price' => 'decimal:2',
        'valid_until' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedDesigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_designer_id');
    }

    public function isExpired(): bool
    {
        return Carbon::today()->isAfter($this->valid_until);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canBeAssigned(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    public static function generateQuoteNumber(): string
    {
        $prefix = 'QT';
        $year = date('Y');
        $month = date('m');
        $lastQuote = self::whereYear('created_at', $year)
                         ->whereMonth('created_at', $month)
                         ->orderBy('id', 'desc')
                         ->first();

        if ($lastQuote) {
            $lastNumber = (int) substr($lastQuote->quote_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s%s%s%04d', $prefix, $year, $month, $newNumber);
    }
}
