<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'total_price',
    ];

    protected $appends = ['total_price', 'product_name'];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    public function getProductNameAttribute(): string
    {
        return $this->attributes['product_name'] ?? ($this->product?->name ?? '');
    }

    public function getTotalPriceAttribute(): ?float
    {
        return (float) ($this->attributes['subtotal'] ?? ($this->unit_price * $this->quantity));
    }

    public function setTotalPriceAttribute($value): void
    {
        $this->attributes['subtotal'] = $value;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
