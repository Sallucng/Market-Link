<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_id',
        'farmer_id',
        'farmer_profile_id',
        'product_id',
        'rating',
        'comment',
        'farmer_response',
        'farmer_reply',
        'responded_at',
        'is_moderated',
        'admin_status',
    ];

    protected $appends = ['admin_status', 'farmer_reply'];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'responded_at' => 'datetime',
            'is_moderated' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($review) {
            if (empty($review->farmer_profile_id) && !empty($review->farmer_id)) {
                $review->farmer_profile_id = $review->farmer_id;
            }
            if (empty($review->farmer_id) && !empty($review->farmer_profile_id)) {
                $review->farmer_id = $review->farmer_profile_id;
            }
        });
    }

    public function getFarmerReplyAttribute(): ?string
    {
        return $this->attributes['farmer_response'] ?? ($this->attributes['farmer_reply'] ?? null);
    }

    public function setFarmerReplyAttribute(?string $value): void
    {
        $this->attributes['farmer_reply'] = $value;
        $this->attributes['farmer_response'] = $value;
        if (!empty($value) && empty($this->attributes['responded_at'])) {
            $this->attributes['responded_at'] = now();
        }
    }

    public function getAdminStatusAttribute(): string
    {
        return ($this->attributes['is_moderated'] ?? false) ? 'hidden' : 'visible';
    }

    public function setAdminStatusAttribute($value): void
    {
        $this->attributes['is_moderated'] = in_array($value, ['hidden', 'flagged', true, 1], true);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_moderated', false);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
