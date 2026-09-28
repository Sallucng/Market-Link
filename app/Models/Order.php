<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'farmer_id',
        'farmer_profile_id',
        'market_id',
        'order_number',
        'order_status',
        'status',
        'pickup_date',
        'pickup_time',
        'pickup_time_slot',
        'pickup_slot_id',
        'total_amount',
        'payment_method',
        'payment_status',
        'cutoff_time',
        'notes',
        'decline_reason',
    ];

    protected $appends = ['status', 'pickup_time'];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'pickup_date' => 'date',
            'cutoff_time' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
            if (empty($order->farmer_profile_id) && !empty($order->farmer_id)) {
                $order->farmer_profile_id = $order->farmer_id;
            }
            if (empty($order->farmer_id) && !empty($order->farmer_profile_id)) {
                $order->farmer_id = $order->farmer_profile_id;
            }
            if (empty($order->pickup_date)) {
                $order->pickup_date = now()->toDateString();
            }
            if (empty($order->pickup_time_slot)) {
                $order->pickup_time_slot = $order->pickup_time ?: '09:00 AM - 11:00 AM';
            }
            if (empty($order->payment_method)) {
                $order->payment_method = 'Cash on Pickup';
            }
            if (empty($order->payment_status)) {
                $order->payment_status = ($order->status === 'completed') ? 'paid' : 'pending';
            }
        });
    }

    /*----------------------------------------------------------------------
    | Accessors & Mutators
    |----------------------------------------------------------------------*/

    public function getStatusAttribute(): string
    {
        if (!empty($this->attributes['status'])) {
            return $this->attributes['status'];
        }
        $legacy = $this->attributes['order_status'] ?? 'pending';
        return $legacy === 'placed' ? 'pending' : $legacy;
    }

    public function setStatusAttribute(?string $value): void
    {
        $this->attributes['status'] = $value;
        $this->attributes['order_status'] = ($value === 'pending') ? 'placed' : $value;
    }

    public function getOrderStatusAttribute($value): string
    {
        return $value ?: ($this->attributes['status'] ?? 'placed');
    }

    public function setOrderStatusAttribute(?string $value): void
    {
        $this->attributes['order_status'] = $value;
        $this->attributes['status'] = ($value === 'placed') ? 'pending' : $value;
    }

    public function getPickupTimeAttribute(): ?string
    {
        return $this->attributes['pickup_time_slot'] ?? ($this->attributes['pickup_time'] ?? null);
    }

    public function setPickupTimeAttribute(?string $value): void
    {
        $this->attributes['pickup_time'] = $value;
        $this->attributes['pickup_time_slot'] = $value;
    }

    public function getPickupTimeSlotAttribute($value): ?string
    {
        return $value ?: ($this->attributes['pickup_time'] ?? null);
    }

    public function setPickupTimeSlotAttribute(?string $value): void
    {
        $this->attributes['pickup_time_slot'] = $value;
        $this->attributes['pickup_time'] = $value;
    }

    public function isPastCutoff(): bool
    {
        if (!$this->cutoff_time) {
            return false;
        }

        return Carbon::now('UTC')->isAfter(
            Carbon::parse($this->cutoff_time)->setTimezone('UTC')
        );
    }

    public function canModifyOrCancel(): bool
    {
        if (in_array($this->status, ['completed', 'cancelled', 'declined'])) {
            return false;
        }

        if ($this->cutoff_time && now()->greaterThan($this->cutoff_time)) {
            return false;
        }

        return true;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid' || in_array($this->status, ['completed', 'ready_for_pickup', 'ready']);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return $this->isPaid() ? 'Paid & Settled' : 'Pending (Due at Pickup)';
    }

    /*----------------------------------------------------------------------
    | Scopes
    |----------------------------------------------------------------------*/

    public function scopePending(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('status', 'pending')
              ->orWhere('order_status', 'placed');
        });
    }

    /*----------------------------------------------------------------------
    | Relationships
    |----------------------------------------------------------------------*/

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class, 'farmer_id');
    }

    public function farmerProfile(): BelongsTo
    {
        return $this->belongsTo(FarmerProfile::class, 'farmer_id');
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class, 'market_id');
    }

    public function pickupSlot(): BelongsTo
    {
        return $this->belongsTo(PickupSlot::class, 'pickup_slot_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'order_id');
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class, 'order_id');
    }

    public function complaint(): HasOne
    {
        return $this->hasOne(Complaint::class, 'order_id');
    }
}
