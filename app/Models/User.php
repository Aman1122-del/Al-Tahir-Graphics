<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    
    // Conditionally use HasRoles trait to avoid issues in testing
    use HasRoles {
        HasRoles::bootHasRoles as protected bootHasRolesParent;
    }
    
    public static function bootHasRoles()
    {
        // Only boot HasRoles if not in testing environment
        if (app()->environment() !== 'testing') {
            static::bootHasRolesParent();
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function assignedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'assigned_designer_id');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function assignedQuotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'assigned_designer_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Chat relationships
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function unreadMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id')->whereNull('read_at');
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isDesigner(): bool
    {
        return $this->hasRole('designer');
    }

    public function isSupport(): bool
    {
        return $this->hasRole('support');
    }

    public function canManageUsers(): bool
    {
        return $this->hasPermissionTo('manage users');
    }

    public function canManageOrders(): bool
    {
        return $this->hasPermissionTo('manage orders');
    }

    public function canManageServices(): bool
    {
        return $this->hasPermissionTo('manage services');
    }

    public function canViewReports(): bool
    {
        return $this->hasPermissionTo('view reports');
    }

    // Chat helper methods
    public function canChat(): bool
    {
        return $this->hasRole(['admin', 'support', 'designer']) || !$this->hasRole(['admin', 'support', 'designer']);
    }

    public function canAccessChats(): bool
    {
        return $this->hasRole(['admin', 'support']);
    }

    public function getUnreadMessageCount(): int
    {
        return $this->unreadMessages()->count();
    }

    public function getChatUsers()
    {
        if ($this->hasRole(['admin', 'support'])) {
            // Admin/support can see all users they've chatted with
            return User::whereHas('sentMessages', function ($query) {
                $query->where('receiver_id', $this->id);
            })->orWhereHas('receivedMessages', function ($query) {
                $query->where('sender_id', $this->id);
            })->where('id', '!=', $this->id)->distinct()->get();
        } else {
            // Regular users can only chat with admin/support
            return User::role(['admin', 'support'])->get();
        }
    }
}
