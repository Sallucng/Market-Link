<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'farmer_id',
        'market_id',
        'order_number',
        'order_status',
        'pickup_date',
        'pickup_time_slot',
        'total_amount',
        'payment_method',
        'payment_status',
        'cutoff_time',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'pickup_date' => 'date',
            'cutoff_time' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function canModifyOrCancel(): bool
    {
        if (in_array($this->order_status, ['completed', 'cancelled', 'declined'])) {
            return false;
        }

        if ($this->cutoff_time && now()->greaterThan($this->cutoff_time)) {
            return false;
        }

        return true;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid' || $this->order_status === 'completed';
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return $this->isPaid() ? 'Paid & Settled' : 'Pending (Due at Pickup)';
    }
}
