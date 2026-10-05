<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'location',
        'city',
        'operating_days',
        'timings',
        'open_time',
        'close_time',
        'latitude',
        'longitude',
        'map_provider',
        'image_url',
        'image',
        'description',
        'status',
    ];

    protected $appends = ['opening_time', 'closing_time'];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($market) {
            if (empty($market->timings) && (!empty($market->open_time) || !empty($market->close_time))) {
                $market->timings = ($market->open_time ?? '08:00 AM') . ' - ' . ($market->close_time ?? '02:00 PM');
            } elseif (empty($market->timings)) {
                $market->timings = '08:00 AM - 02:00 PM';
            }
            if (empty($market->open_time) && !empty($market->timings)) {
                $parts = explode('-', $market->timings);
                $market->open_time = trim($parts[0]);
                $market->close_time = isset($parts[1]) ? trim($parts[1]) : '02:00 PM';
            }
            if (empty($market->location) && !empty($market->address)) {
                $market->location = $market->address;
            }
            if (empty($market->address) && !empty($market->location)) {
                $market->address = $market->location;
            }
            if (empty($market->latitude)) {
                $market->latitude = 40.7128;
            }
            if (empty($market->longitude)) {
                $market->longitude = -74.0060;
            }
        });
    }

    public function getOperatingDaysAttribute($value)
    {
        return new \App\Support\OperatingDays($value);
    }

    public function setOperatingDaysAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['operating_days'] = json_encode($value);
        } else {
            $this->attributes['operating_days'] = $value;
        }
    }

    public function getLocationAttribute($value): ?string
    {
        return $value ?: $this->address;
    }

    public function getImageAttribute($value): ?string
    {
        return $this->image_url;
    }

    public function getImageUrlAttribute(?string $value): ?string
    {
        $val = $value ?? ($this->attributes['image'] ?? null);
        if (!empty($val)) {
            if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
                return $val;
            }
            if (file_exists(public_path('storage/' . $val))) {
                return asset('storage/' . $val);
            }
            if (file_exists(public_path($val))) {
                return asset($val);
            }
        }

        $fallbacks = [
            1 => 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=800&q=80',
            2 => 'https://images.unsplash.com/photo-1533900298318-6b8da08a523e?auto=format&fit=crop&w=800&q=80',
            3 => 'https://images.unsplash.com/photo-1516253593875-bd7ba052fbc5?auto=format&fit=crop&w=800&q=80',
        ];

        return $fallbacks[$this->id ?? 1] ?? 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=800&q=80';
    }

    public function setImageAttribute(?string $value): void
    {
        $this->attributes['image'] = $value;
        $this->attributes['image_url'] = $value;
    }

    public function getOpeningTimeAttribute(): ?string
    {
        return $this->open_time ?: ($this->timings ? explode('-', $this->timings)[0] : '08:00');
    }

    public function setOpeningTimeAttribute(?string $value): void
    {
        $this->attributes['open_time'] = $value;
    }

    public function getClosingTimeAttribute(): ?string
    {
        return $this->close_time ?: ($this->timings && str_contains($this->timings, '-') ? explode('-', $this->timings)[1] : '14:00');
    }

    public function setClosingTimeAttribute(?string $value): void
    {
        $this->attributes['close_time'] = $value;
    }

    /**
     * Scope: find markets within a radius (km) of given coordinates.
     * Uses a bounding-box filter (works on SQLite and MySQL alike).
     * Results are ordered by approximate distance (Euclidean on lat/lng).
     */
    public function scopeNearby(Builder $query, float $lat, float $lng, float $radiusKm = 25): Builder
    {
        // ~1 degree latitude  ≈ 111.32 km
        // ~1 degree longitude ≈ 111.32 * cos(lat) km
        $latDelta = $radiusKm / 111.32;
        $lngDelta = $radiusKm / (111.32 * cos(deg2rad($lat)));

        return $query
            ->whereBetween('latitude',  [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta])
            ->orderByRaw(
                "(latitude - ?) * (latitude - ?) + (longitude - ?) * (longitude - ?)",
                [$lat, $lat, $lng, $lng]
            );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function farmers(): HasMany
    {
        return $this->hasMany(Farmer::class);
    }

    public function farmerProfiles(): HasMany
    {
        return $this->hasMany(FarmerProfile::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'market_id');
    }
}
