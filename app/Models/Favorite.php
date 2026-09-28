<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'user_id',
        'farmer_id',
        'product_id',
        'market_id',
        'item_type',
        'item_id',
        'favoritable_type',
        'favoritable_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($fav) {
            // Synchronize user_id and customer_id
            if (empty($fav->customer_id) && !empty($fav->user_id)) {
                $fav->customer_id = $fav->user_id;
            }
            if (empty($fav->user_id) && !empty($fav->customer_id)) {
                $fav->user_id = $fav->customer_id;
            }

            // Polymorphic resolution
            if (!empty($fav->favoritable_type) && !empty($fav->favoritable_id)) {
                if (str_contains($fav->favoritable_type, 'Farmer')) {
                    $fav->item_type = 'farmer';
                    $fav->item_id = $fav->favoritable_id;
                    $fav->farmer_id = $fav->favoritable_id;
                } elseif (str_contains($fav->favoritable_type, 'Product')) {
                    $fav->item_type = 'product';
                    $fav->item_id = $fav->favoritable_id;
                    $fav->product_id = $fav->favoritable_id;
                } elseif (str_contains($fav->favoritable_type, 'Market')) {
                    $fav->item_type = 'market';
                    $fav->item_id = $fav->favoritable_id;
                    $fav->market_id = $fav->favoritable_id;
                }
            } else {
                if (!empty($fav->farmer_id)) {
                    $fav->item_type = 'farmer';
                    $fav->item_id = $fav->farmer_id;
                    $fav->favoritable_type = \App\Models\FarmerProfile::class;
                    $fav->favoritable_id = $fav->farmer_id;
                } elseif (!empty($fav->product_id)) {
                    $fav->item_type = 'product';
                    $fav->item_id = $fav->product_id;
                    $fav->favoritable_type = \App\Models\Product::class;
                    $fav->favoritable_id = $fav->product_id;
                } elseif (!empty($fav->market_id)) {
                    $fav->item_type = 'market';
                    $fav->item_id = $fav->market_id;
                    $fav->favoritable_type = \App\Models\Market::class;
                    $fav->favoritable_id = $fav->market_id;
                } elseif (!empty($fav->item_type) && !empty($fav->item_id)) {
                    if ($fav->item_type === 'farmer') {
                        $fav->farmer_id = $fav->item_id;
                        $fav->favoritable_type = \App\Models\FarmerProfile::class;
                        $fav->favoritable_id = $fav->item_id;
                    } elseif ($fav->item_type === 'product') {
                        $fav->product_id = $fav->item_id;
                        $fav->favoritable_type = \App\Models\Product::class;
                        $fav->favoritable_id = $fav->item_id;
                    } elseif ($fav->item_type === 'market') {
                        $fav->market_id = $fav->item_id;
                        $fav->favoritable_type = \App\Models\Market::class;
                        $fav->favoritable_id = $fav->item_id;
                    }
                }
            }
        });
    }

    public function favoritable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class, 'market_id');
    }
}
