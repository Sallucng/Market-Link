<?php

namespace Database\Seeders;

use App\Models\Farmer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $farmers = Farmer::whereHas('user', function ($q) {
            $q->where('is_approved', true);
        })->with('products')->take(3)->get();

        if ($farmers->isEmpty()) {
            return;
        }

        // Farmer 1 Sale (Spotlighted initially)
        $farmer1 = $farmers[0];
        $sale1 = Sale::updateOrCreate(
            ['farmer_id' => $farmer1->id, 'title' => 'Autumn Harvest Flash Sale'],
            [
                'description' => 'Save 20% on our heirloom harvest varieties, freshly picked seasonal vegetables, and greenhouse greens!',
                'discount_percentage' => 20,
                'badge_label' => '20% OFF HARVEST',
                'banner_image' => 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=1200&q=80',
                'start_date' => now()->subDay()->toDateString(),
                'end_date' => now()->addDays(6)->toDateString(),
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        $f1Products = Product::where('farmer_id', $farmer1->id)->take(3)->pluck('id')->toArray();
        if (!empty($f1Products)) {
            $sale1->products()->sync($f1Products);
        }

        // Farmer 2 Sale (Active, ready for Admin to toggle/spotlight)
        if ($farmers->count() > 1) {
            $farmer2 = $farmers[1];
            $sale2 = Sale::updateOrCreate(
                ['farmer_id' => $farmer2->id, 'title' => 'Weekend Berry & Greens Special'],
                [
                    'description' => 'Stock up on sweet organic berries, fresh orchard fruits, and crisp farm-grown salads this market day.',
                    'discount_percentage' => 25,
                    'badge_label' => '25% OFF WEEKEND',
                    'banner_image' => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=1200&q=80',
                    'start_date' => now()->toDateString(),
                    'end_date' => now()->addDays(5)->toDateString(),
                    'is_active' => true,
                    'is_featured' => false,
                ]
            );

            $f2Products = Product::where('farmer_id', $farmer2->id)->take(2)->pluck('id')->toArray();
            if (!empty($f2Products)) {
                $sale2->products()->sync($f2Products);
            }
        }
    }
}
