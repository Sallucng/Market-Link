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
        return $this->image_url;
    }

    public function getImageUrlAttribute(?string $value): ?string
    {
        $val = $value ?? ($this->attributes['image'] ?? null);
        if (empty($val)) {
            return $this->getDefaultProduceImage();
        }

        if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
            return $val;
        }

        if (file_exists(public_path('storage/' . $val))) {
            return asset('storage/' . $val);
        }
        if (file_exists(public_path($val))) {
            return asset($val);
        }

        return $this->getProduceImageByPath($val);
    }

    protected function getProduceImageByPath(string $path): string
    {
        $images = [
            'heirloom_tomatoes' => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=800&q=80',
            'romaine_lettuce'   => 'https://images.unsplash.com/photo-1556801712-76c8eb07bbc9?auto=format&fit=crop&w=800&q=80',
            'rainbow_carrots'   => 'https://images.unsplash.com/photo-1447175008436-054170c2e979?auto=format&fit=crop&w=800&q=80',
            'honeycrisp_apples' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=800&q=80',
            'strawberries'      => 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?auto=format&fit=crop&w=800&q=80',
            'yellow_peaches'    => 'https://images.unsplash.com/photo-1595152772835-219674b2a8a6?auto=format&fit=crop&w=800&q=80',
            'goat_cheese'       => 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?auto=format&fit=crop&w=800&q=80',
            'pasture_butter'    => 'https://images.unsplash.com/photo-1589985270826-4b7bb135bc9d?auto=format&fit=crop&w=800&q=80',
            'pasture_eggs'      => 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?auto=format&fit=crop&w=800&q=80',
            'whole_chicken'     => 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?auto=format&fit=crop&w=800&q=80',
            'sweet_basil'       => 'https://images.unsplash.com/photo-1749655248287-d1e0acb5f8d1?auto=format&fit=crop&w=800&q=80',
            'rosemary'          => 'https://images.unsplash.com/photo-1764488034691-eda628fbdb7c?auto=format&fit=crop&w=800&q=80',
            'wild_arugula'      => 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&w=800&q=80',
            'swiss_chard'       => 'https://images.unsplash.com/photo-1579113800032-c38bd7635818?auto=format&fit=crop&w=800&q=80',
            'golden_beets'      => 'https://images.unsplash.com/photo-1593105544559-ecb03bf76f82?auto=format&fit=crop&w=800&q=80',
        ];

        foreach ($images as $key => $url) {
            if (str_contains($path, $key)) {
                return $url;
            }
        }

        return $this->getDefaultProduceImage();
    }

    protected function getDefaultProduceImage(): string
    {
        $fallbacks = [
            1  => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=800&q=80', // Heirloom Tomatoes
            2  => 'https://images.unsplash.com/photo-1556801712-76c8eb07bbc9?auto=format&fit=crop&w=800&q=80', // Romaine
            3  => 'https://images.unsplash.com/photo-1447175008436-054170c2e979?auto=format&fit=crop&w=800&q=80', // Carrots
            4  => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=800&q=80', // Apples
            5  => 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?auto=format&fit=crop&w=800&q=80', // Strawberries
            6  => 'https://images.unsplash.com/photo-1595152772835-219674b2a8a6?auto=format&fit=crop&w=800&q=80', // Peaches
            7  => 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?auto=format&fit=crop&w=800&q=80', // Cheese
            8  => 'https://images.unsplash.com/photo-1589985270826-4b7bb135bc9d?auto=format&fit=crop&w=800&q=80', // Butter
            9  => 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?auto=format&fit=crop&w=800&q=80', // Eggs
            10 => 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?auto=format&fit=crop&w=800&q=80', // Chicken
            11 => 'https://images.unsplash.com/photo-1749655248287-d1e0acb5f8d1?auto=format&fit=crop&w=800&q=80', // Basil
            12 => 'https://images.unsplash.com/photo-1764488034691-eda628fbdb7c?auto=format&fit=crop&w=800&q=80', // Rosemary
            13 => 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&w=800&q=80', // Arugula
            14 => 'https://images.unsplash.com/photo-1579113800032-c38bd7635818?auto=format&fit=crop&w=800&q=80', // Swiss Chard
            15 => 'https://images.unsplash.com/photo-1593105544559-ecb03bf76f82?auto=format&fit=crop&w=800&q=80', // Beets
        ];
        $id = $this->id ?? abs(crc32($this->name ?? '1'));
        $idx = (($id - 1) % count($fallbacks)) + 1;
        return $fallbacks[$idx] ?? $fallbacks[1];
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
