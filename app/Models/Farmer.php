<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farmer extends Model
{
    use HasFactory;

    protected $table = 'farmers';

    protected $fillable = [
        'user_id',
        'market_id',
        'stall_name',
        'business_name',
        'farm_name',
        'contact_person',
        'contact_number',
        'stall_number',
        'business_license',
        'address',
        'city',
        'state',
        'postal_code',
        'latitude',
        'longitude',
        'operating_days',
        'pickup_time_windows',
        'pickup_start_time',
        'pickup_end_time',
        'order_cutoff_time',
        'cutoff_hours',
        'bio',
        'image_url',
        'is_approved',
        'approval_status',
        'rejection_reason',
        'settings',
    ];

    protected $appends = ['farm_name', 'approval_status', 'cover_image_url'];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'cutoff_hours' => 'integer',
            'operating_days' => 'array',
            'is_approved' => 'boolean',
            'settings' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($farmer) {
            if (empty($farmer->business_name) && !empty($farmer->stall_name)) {
                $farmer->business_name = $farmer->stall_name;
            }
            if (empty($farmer->farm_name) && !empty($farmer->stall_name)) {
                $farmer->farm_name = $farmer->stall_name;
            }
            if (empty($farmer->stall_name) && !empty($farmer->business_name)) {
                $farmer->stall_name = $farmer->business_name;
            }
        });
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? $default;
    }

    public function setSetting(string $key, mixed $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        $this->save();
    }

    public function getFarmNameAttribute(): ?string
    {
        return $this->attributes['farm_name'] ?? ($this->attributes['business_name'] ?? ($this->attributes['stall_name'] ?? null));
    }

    public function setFarmNameAttribute(?string $value): void
    {
        $this->attributes['farm_name'] = $value;
        $this->attributes['business_name'] = $value;
        $this->attributes['stall_name'] = $value;
    }

    public function getBusinessNameAttribute(): ?string
    {
        return $this->attributes['business_name'] ?? ($this->attributes['farm_name'] ?? ($this->attributes['stall_name'] ?? null));
    }

    public function setBusinessNameAttribute(?string $value): void
    {
        $this->attributes['business_name'] = $value;
        $this->attributes['farm_name'] = $value;
        $this->attributes['stall_name'] = $value;
    }

    public function getApprovalStatusAttribute(): string
    {
        if (isset($this->attributes['approval_status']) && !empty($this->attributes['approval_status'])) {
            return $this->attributes['approval_status'];
        }
        return ($this->attributes['is_approved'] ?? true) ? 'approved' : 'pending';
    }

    public function setApprovalStatusAttribute($value): void
    {
        $this->attributes['approval_status'] = (string) $value;
        $this->attributes['is_approved'] = ($value === 'approved' || $value === true || $value === 1);
    }

    public function getCoverImageUrlAttribute(): string
    {
        if (!empty($this->attributes['cover_image_url'])) {
            return $this->attributes['cover_image_url'];
        }
        if (!empty($this->settings['cover_image_url'])) {
            return $this->settings['cover_image_url'];
        }

        $covers = [
            1 => 'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=800&q=80',
            2 => 'https://images.unsplash.com/photo-1618160702438-9b02ab6515c9?auto=format&fit=crop&w=800&q=80',
            3 => 'https://images.unsplash.com/photo-1530836369250-ef72a3f5cda8?auto=format&fit=crop&w=800&q=80',
            4 => 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=800&q=80',
            5 => 'https://images.unsplash.com/photo-1582284540020-8acbe03f4924?auto=format&fit=crop&w=800&q=80',
            6 => 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=800&q=80',
            7 => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=800&q=80',
            8 => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&w=800&q=80',
            9 => 'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?auto=format&fit=crop&w=800&q=80',
            10 => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800&q=80',
            11 => 'https://images.unsplash.com/photo-1527842891421-42eec6e703ea?auto=format&fit=crop&w=800&q=80',
            12 => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=800&q=80',
            13 => 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=800&q=80',
            14 => 'https://images.unsplash.com/photo-1500651230702-0e2d8a49d4ad?auto=format&fit=crop&w=800&q=80',
        ];

        return $covers[$this->id] ?? 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800&q=80';
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_approved', true)
            ->whereHas('user', function (Builder $q) {
                $q->where('is_active', true);
            });
    }

    public function getIsActiveAttribute(): bool
    {
        return (bool) ($this->user->is_active ?? true);
    }

    public function farmerProfile(): BelongsTo
    {
        return $this->belongsTo(Farmer::class, 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function markets(): BelongsToMany
    {
        return $this->belongsToMany(Market::class, 'farmer_market', 'farmer_profile_id', 'market_id')
            ->withPivot(['assigned_stall', 'status'])
            ->withTimestamps();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function averageRating(): float
    {
        return (float) $this->reviews()->avg('rating') ?: 5.0;
    }
}
