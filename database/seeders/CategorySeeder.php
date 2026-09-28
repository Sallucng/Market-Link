<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Vegetables',
                'description' => 'Locally grown organic vegetables, leafy greens, root crops, and seasonal produce.',
            ],
            [
                'name'        => 'Fruits',
                'description' => 'Orchard-fresh fruits, vine-ripened berries, melons, and seasonal citrus.',
            ],
            [
                'name'        => 'Dairy',
                'description' => 'Farm-fresh raw & pasteurized milk, artisanal butter, yogurts, and handcrafted cheeses.',
            ],
            [
                'name'        => 'Poultry',
                'description' => 'Pasture-raised organic poultry, duck, and farm-fresh free-range eggs.',
            ],
            [
                'name'        => 'Organic Herbs',
                'description' => 'Freshly harvested culinary and medicinal herbs, microgreens, and natural seasonings.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                [
                    'slug'        => Str::slug($cat['name']),
                    'description' => $cat['description'],
                    'is_active'   => true,
                ]
            );
        }
    }
}
