<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'operating_days',
        'timings',
        'latitude',
        'longitude',
        'map_provider',
        'image_url',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function farmers(): HasMany
    {
        return $this->hasMany(Farmer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
