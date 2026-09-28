<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'contact_number',
        'phone',
        'address',
        'role',
        'status',
        'is_active',
        'is_approved',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_approved' => 'boolean',
            'preferences' => 'array',
        ];
    }

    public function getPreference(string $key, mixed $default = null): mixed
    {
        $prefs = $this->preferences ?? [];
        return $prefs[$key] ?? $default;
    }

    public function setPreference(string $key, mixed $value): void
    {
        $prefs = $this->preferences ?? [];
        $prefs[$key] = $value;
        $this->preferences = $prefs;
        $this->save();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isFarmer(): bool
    {
        return $this->role === 'farmer';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function isPending(): bool
    {
        return !(bool) $this->is_approved;
    }

    public function isSuspended(): bool
    {
        return !(bool) $this->is_active;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->contact_number;
    }

    public function setPhoneAttribute(?string $val): void
    {
        $this->attributes['contact_number'] = $val;
    }

    public function getStatusAttribute($value): string
    {
        if (!empty($value)) {
            return $value;
        }
        if (!$this->is_active) {
            return 'suspended';
        }
        return $this->is_approved ? 'active' : 'pending';
    }

    public function setStatusAttribute(?string $val): void
    {
        $this->attributes['status'] = $val;
        if ($val === 'suspended') {
            $this->attributes['is_active'] = false;
        } elseif ($val === 'pending') {
            $this->attributes['is_active'] = true;
            $this->attributes['is_approved'] = false;
        } else {
            $this->attributes['is_active'] = true;
            $this->attributes['is_approved'] = true;
        }
    }

    public function farmer(): HasOne
    {
        return $this->hasOne(Farmer::class);
    }

    public function farmerProfile(): HasOne
    {
        return $this->hasOne(FarmerProfile::class, 'user_id');
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, Farmer::class, 'user_id', 'farmer_id');
    }

    public function farmerOrders()
    {
        return $this->hasManyThrough(Order::class, Farmer::class, 'user_id', 'farmer_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class, 'customer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function customerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'customer_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function customerConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'customer_id');
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'customer_id');
    }

    public function unreadMessagesCount(): int
    {
        if ($this->isFarmer() && $this->farmer) {
            return Message::whereHas('conversation', function ($q) {
                $q->where('farmer_id', $this->farmer->id);
            })->where('sender_id', '!=', $this->id)->where('is_read', false)->count();
        }

        return Message::whereHas('conversation', function ($q) {
            $q->where('customer_id', $this->id);
        })->where('sender_id', '!=', $this->id)->where('is_read', false)->count();
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeFarmer($query)
    {
        return $query->where('role', 'farmer');
    }

    public function scopeCustomer($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
