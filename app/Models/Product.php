<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'farmer_profile_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'unit',
        'stock_quantity',
        'weekly_recurring_stock',
        'weekly_quota',
        'weekly_stock',
        'image',
        'image_path',
        'image_url',
        'status',
        'is_sold_out',
        'is_available',
        'is_moderated',
    ];

    protected $appends = ['weekly_stock', 'image', 'status'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'weekly_recurring_stock' => 'integer',
            'weekly_quota' => 'integer',
            'is_sold_out' => 'boolean',
            'is_available' => 'boolean',
            'is_moderated' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            if (empty($product->slug) && !empty($product->name)) {
                $product->slug = Str::slug($product->name) . '-' . rand(100, 999);
            }
            if (empty($product->farmer_profile_id) && !empty($product->farmer_id)) {
                $product->farmer_profile_id = $product->farmer_id;
            }
            if (empty($product->farmer_id) && !empty($product->farmer_profile_id)) {
                $product->farmer_id = $product->farmer_profile_id;
            }
        });
    }

    /*----------------------------------------------------------------------
    | Derived attributes & Aliases
    |----------------------------------------------------------------------*/

    public function getWeeklyStockAttribute(): ?int
    {
        return $this->attributes['weekly_recurring_stock'] ?? ($this->attributes['weekly_quota'] ?? 0);
    }

    public function setWeeklyStockAttribute(?int $value): void
    {
        $this->attributes['weekly_recurring_stock'] = $value;
        $this->attributes['weekly_quota'] = $value;
    }

    public function getWeeklyQuotaAttribute(): ?int
    {
        return $this->attributes['weekly_quota'] ?? ($this->attributes['weekly_recurring_stock'] ?? 0);
    }

    public function setWeeklyQuotaAttribute(?int $value): void
    {
        $this->attributes['weekly_quota'] = $value;
        $this->attributes['weekly_recurring_stock'] = $value;
    }

    public function getImageAttribute(): ?string
    {
        return $this->attributes['image_url'] ?? ($this->attributes['image'] ?? null);
    }

    public function setImageAttribute(?string $value): void
    {
        $this->attributes['image'] = $value;
        $this->attributes['image_url'] = $value;
    }

    public function getImagePathAttribute(): ?string
    {
        return $this->image;
    }

    public function setImagePathAttribute(?string $value): void
    {
        $this->setImageAttribute($value);
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_moderated) {
            return 'flagged';
        }
        if (isset($this->attributes['is_sold_out']) && $this->attributes['is_sold_out']) {
            return 'sold_out';
        }
        return $this->attributes['status'] ?? 'available';
    }

    public function setStatusAttribute(?string $value): void
    {
        $this->attributes['status'] = $value;
        if ($value === 'flagged') {
            $this->attributes['is_moderated'] = true;
            $this->attributes['is_available'] = false;
        } elseif ($value === 'sold_out') {
            $this->attributes['is_sold_out'] = true;
            $this->attributes['is_available'] = false;
            $this->attributes['is_moderated'] = false;
        } elseif ($value === 'available') {
            $this->attributes['is_sold_out'] = false;
            $this->attributes['is_available'] = true;
            $this->attributes['is_moderated'] = false;
        } elseif ($value === 'unavailable') {
            $this->attributes['is_available'] = false;
        }
    }

    public function getIsAvailableAttribute(): bool
    {
        if (isset($this->attributes['is_sold_out']) && $this->attributes['is_sold_out']) {
            return false;
        }
        return ($this->attributes['status'] ?? 'available') === 'available';
    }

    public function setIsAvailableAttribute($value): void
    {
        $this->attributes['is_available'] = (bool) $value;
        if ($value) {
            $this->attributes['status'] = 'available';
            $this->attributes['is_sold_out'] = false;
        } else {
            $this->attributes['status'] = 'unavailable';
        }
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('status', 'available')
              ->orWhereNull('status');
        })->where('is_sold_out', false)
          ->where('is_available', true)
          ->where(function ($q) {
              $q->where('is_moderated', false)
                ->orWhereNull('is_moderated');
          });
    }

    /*----------------------------------------------------------------------
    | Relationships
    |----------------------------------------------------------------------*/

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class, 'farmer_id');
    }

    public function farmerProfile(): BelongsTo
    {
        return $this->belongsTo(FarmerProfile::class, 'farmer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function sales(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Sale::class, 'product_sale')->withTimestamps();
    }
}
