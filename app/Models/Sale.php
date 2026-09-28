<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'title',
        'description',
        'discount_percentage',
        'badge_label',
        'banner_image',
        'start_date',
        'end_date',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'discount_percentage' => 'integer',
    ];

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_sale')->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        $today = now()->toDateString();
        return $query->where('is_active', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get computed badge text (e.g. "25% OFF" or custom label)
     */
    public function getComputedBadgeAttribute(): string
    {
        if (!empty($this->badge_label)) {
            return $this->badge_label;
        }
        if (!empty($this->discount_percentage)) {
            return $this->discount_percentage . '% OFF';
        }
        return 'SPECIAL OFFER';
    }

    /**
     * Check if currently within active promotion dates
     */
    public function isRunning(): bool
    {
        $today = now()->startOfDay();
        return $this->is_active && $this->start_date <= $today && $this->end_date >= $today;
    }

    /**
     * Status label for display (Active, Scheduled, Expired, Paused)
     */
    public function getStatusLabelAttribute(): string
    {
        $today = now()->startOfDay();
        if (!$this->is_active) {
            return 'Paused';
        }
        if ($this->start_date > $today) {
            return 'Scheduled';
        }
        if ($this->end_date < $today) {
            return 'Expired';
        }
        return 'Active';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status_label) {
            'Active' => 'success',
            'Scheduled' => 'info',
            'Paused' => 'warning',
            'Expired' => 'secondary',
            default => 'light',
        };
    }
}
