<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModel($query, $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getActionLabelAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->action));
    }

    public function getModelNameAttribute(): string
    {
        if ($this->model_type) {
            return class_basename($this->model_type);
        }
        return 'N/A';
    }

    public function hasDataChanges(): bool
    {
        return !empty($this->old_values) || !empty($this->new_values);
    }

    public function getChangesSummaryAttribute(): string
    {
        if (!$this->hasDataChanges()) {
            return 'No changes';
        }

        $changes = [];
        
        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $field => $newValue) {
                $oldValue = $this->old_values[$field] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[] = ucfirst($field) . ': ' . $oldValue . ' → ' . $newValue;
                }
            }
        }

        return implode(', ', $changes);
    }
}
