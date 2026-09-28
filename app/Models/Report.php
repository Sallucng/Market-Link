<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'report_type',
        'report_date',
        'total_orders',
        'total_revenue',
        'active_farmers',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'total_orders' => 'integer',
            'total_revenue' => 'decimal:2',
            'active_farmers' => 'integer',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
