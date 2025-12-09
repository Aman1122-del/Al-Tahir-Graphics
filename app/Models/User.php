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
        'is_admin',
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
            'is_admin' => 'boolean',
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

    // UnifiedChat relationships
    public function chats(): HasMany
    {
        return $this->hasMany(UnifiedChat::class, 'created_by');
    }

    public function assignedChats(): HasMany
    {
        return $this->hasMany(UnifiedChat::class, 'assigned_to');
    }

    public function chatParticipants(): HasMany
    {
        return $this->hasMany(ChatParticipant::class, 'user_id');
    }

    public function sentChatMessages(): HasMany
    {
        return $this->hasMany(UnifiedChatMessage::class, 'sender_id');
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function isDesigner(): bool
    {
        return $this->is_admin; // For now, treat admin as designer too
    }

    public function isSupport(): bool
    {
        return $this->is_admin; // For now, treat admin as support too
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
        return true; // All users can chat
    }

    public function canAccessChats(): bool
    {
        return true; // Allow all authenticated users to access chats
    }

    public function getUnreadMessageCount(): int
    {
        return $this->unreadMessages()->count();
    }

    public function getChatUsers()
    {
        if ($this->is_admin) {
            // Admin can see all users who have participated in chats
            return User::whereHas('chatParticipants', function ($query) {
                $query->whereHas('chat', function ($chatQuery) {
                    $chatQuery->whereHas('participants', function ($participantQuery) {
                        $participantQuery->where('user_id', $this->id);
                    });
                });
            })->where('id', '!=', $this->id)->distinct()->get();
        } else {
            // Regular users can only chat with admin
            return User::where('is_admin', true)->get();
        }
    }

    public function hasRole($roles): bool
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        // For now, treat admin as having all roles
        if ($this->is_admin) {
            return true;
        }
        
        // Regular users don't have admin/support roles
        return false;
    }
}
