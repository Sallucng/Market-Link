<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'farmer_id',
        'order_id',
        'complaint_type',
        'subject',
        'description',
        'status',
        'admin_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class, 'farmer_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeDismissed($query)
    {
        return $query->where('status', 'dismissed');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->complaint_type) {
            'poor_quality' => 'Poor Quality Produce',
            'unfulfilled_order' => 'Unfulfilled / Incomplete Order',
            'pricing_issue' => 'Pricing / Billing Discrepancy',
            'unprofessional_conduct' => 'Unprofessional Conduct',
            'inaccurate_listing' => 'Inaccurate Product Listing',
            default => 'General Complaint / Other',
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => [
                'label' => 'Pending Review',
                'bg' => 'bg-warning text-dark border-warning',
                'icon' => 'bi-hourglass-split',
            ],
            'under_review' => [
                'label' => 'Under Review',
                'bg' => 'bg-primary text-white border-primary',
                'icon' => 'bi-search',
            ],
            'resolved' => [
                'label' => 'Resolved',
                'bg' => 'bg-success text-white border-success',
                'icon' => 'bi-check-circle-fill',
            ],
            'dismissed' => [
                'label' => 'Dismissed',
                'bg' => 'bg-secondary text-white border-secondary',
                'icon' => 'bi-slash-circle',
            ],
            default => [
                'label' => ucfirst($this->status),
                'bg' => 'bg-secondary text-white border-secondary',
                'icon' => 'bi-info-circle',
            ],
        };
    }
}
