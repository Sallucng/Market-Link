<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $approvedFarmers = FarmerProfile::where('is_approved', true)->get();
        $categories = Category::all()->keyBy('name');

        if ($approvedFarmers->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $farmer1 = $approvedFarmers[0];
        $farmer2 = $approvedFarmers[1] ?? $farmer1;
        $farmer3 = $approvedFarmers[2] ?? $farmer1;
        $farmer4 = $approvedFarmers[3] ?? $farmer1;
        $farmer5 = $approvedFarmers[4] ?? $farmer1;

        $catVeg = $categories->get('Vegetables');
        $catFruit = $categories->get('Fruits');
        $catDairy = $categories->get('Dairy');
        $catPoultry = $categories->get('Poultry');
        $catHerbs = $categories->get('Organic Herbs');

        $products = [
            // Farmer 1 (Vegetables & Greens)
            [
                'farmer_profile_id' => $farmer1->id,
                'category_id'       => $catVeg->id,
                'name'              => 'Organic Heirloom Tomatoes',
                'description'       => 'Vine-ripened multicolor heirloom tomatoes with rich sweet acidity.',
                'price'             => 4.99,
                'unit'              => 'kg',
                'stock_quantity'    => 60,
                'weekly_quota'      => 80,
                'image_path'        => 'products/heirloom_tomatoes.jpg',
            ],
            [
                'farmer_profile_id' => $farmer1->id,
                'category_id'       => $catVeg->id,
                'name'              => 'Crisp Romaine Hearts',
                'description'       => 'Fresh harvested organic romaine heads with crunchy sweet ribs.',
                'price'             => 2.75,
                'unit'              => 'head',
                'stock_quantity'    => 45,
                'weekly_quota'      => 60,
                'image_path'        => 'products/romaine_lettuce.jpg',
            ],
            [
                'farmer_profile_id' => $farmer1->id,
                'category_id'       => $catVeg->id,
                'name'              => 'Baby Rainbow Carrots',
                'description'       => 'Sweet tender bunch of purple, yellow, and orange nantes carrots with tops.',
                'price'             => 3.50,
                'unit'              => 'bunch',
                'stock_quantity'    => 50,
                'weekly_quota'      => 75,
                'image_path'        => 'products/rainbow_carrots.jpg',
            ],

            // Farmer 2 (Fruits & Berries)
            [
                'farmer_profile_id' => $farmer2->id,
                'category_id'       => $catFruit->id,
                'name'              => 'Sweet Honeycrisp Apples',
                'description'       => 'Tree-ripened, extra crisp and juicy orchard apples.',
                'price'             => 3.80,
                'unit'              => 'kg',
                'stock_quantity'    => 80,
                'weekly_quota'      => 100,
                'image_path'        => 'products/honeycrisp_apples.jpg',
            ],
            [
                'farmer_profile_id' => $farmer2->id,
                'category_id'       => $catFruit->id,
                'name'              => 'California Strawberries',
                'description'       => 'Sweet fragrant coastal strawberries hand-picked at peak ripeness.',
                'price'             => 5.25,
                'unit'              => 'box',
                'stock_quantity'    => 40,
                'weekly_quota'      => 60,
                'image_path'        => 'products/strawberries.jpg',
            ],
            [
                'farmer_profile_id' => $farmer2->id,
                'category_id'       => $catFruit->id,
                'name'              => 'Sun-Ripened Yellow Peaches',
                'description'       => 'Freestone sweet fragrant peaches bursting with rich juice.',
                'price'             => 4.50,
                'unit'              => 'kg',
                'stock_quantity'    => 35,
                'weekly_quota'      => 50,
                'image_path'        => 'products/yellow_peaches.jpg',
            ],

            // Farmer 3 (Dairy & Poultry)
            [
                'farmer_profile_id' => $farmer3->id,
                'category_id'       => $catDairy->id,
                'name'              => 'Artisan Goat Milk Cheese',
                'description'       => 'Creamy, tangy fresh chevre rolled in fine culinary sea salt.',
                'price'             => 6.50,
                'unit'              => 'piece',
                'stock_quantity'    => 30,
                'weekly_quota'      => 40,
                'image_path'        => 'products/goat_cheese.jpg',
            ],
            [
                'farmer_profile_id' => $farmer3->id,
                'category_id'       => $catDairy->id,
                'name'              => 'Grass-Fed Pasture Butter',
                'description'       => 'Cultured rich yellow farm butter with natural sweet churned aroma.',
                'price'             => 7.00,
                'unit'              => 'piece',
                'stock_quantity'    => 25,
                'weekly_quota'      => 35,
                'image_path'        => 'products/pasture_butter.jpg',
            ],
            [
                'farmer_profile_id' => $farmer3->id,
                'category_id'       => $catPoultry->id,
                'name'              => 'Pasture-Raised Brown Eggs',
                'description'       => 'Large brown eggs from free-ranging hens with deep golden yolks.',
                'price'             => 6.00,
                'unit'              => 'dozen',
                'stock_quantity'    => 50,
                'weekly_quota'      => 70,
                'image_path'        => 'products/pasture_eggs.jpg',
            ],
            [
                'farmer_profile_id' => $farmer3->id,
                'category_id'       => $catPoultry->id,
                'name'              => 'Free-Range Whole Chicken',
                'description'       => 'Slow-grown antibiotic-free whole heritage roasting chicken.',
                'price'             => 16.50,
                'unit'              => 'piece',
                'stock_quantity'    => 20,
                'weekly_quota'      => 30,
                'image_path'        => 'products/whole_chicken.jpg',
            ],

            // Farmer 4 (Herbs & Specialty Greens)
            [
                'farmer_profile_id' => $farmer4->id,
                'category_id'       => $catHerbs->id,
                'name'              => 'Genovese Sweet Basil',
                'description'       => 'Aromatic sweet basil bunch, ideal for traditional pesto and sauces.',
                'price'             => 2.50,
                'unit'              => 'bunch',
                'stock_quantity'    => 40,
                'weekly_quota'      => 50,
                'image_path'        => 'products/sweet_basil.jpg',
            ],
            [
                'farmer_profile_id' => $farmer4->id,
                'category_id'       => $catHerbs->id,
                'name'              => 'Fresh Garden Rosemary',
                'description'       => 'Fragrant woody rosemary sprigs harvested morning of market.',
                'price'             => 2.25,
                'unit'              => 'bunch',
                'stock_quantity'    => 35,
                'weekly_quota'      => 45,
                'image_path'        => 'products/rosemary.jpg',
            ],
            [
                'farmer_profile_id' => $farmer4->id,
                'category_id'       => $catHerbs->id,
                'name'              => 'Spicy Wild Arugula',
                'description'       => 'Peppery field-grown salad arugula leaves full of pungent flavor.',
                'price'             => 3.20,
                'unit'              => 'bunch',
                'stock_quantity'    => 30,
                'weekly_quota'      => 50,
                'image_path'        => 'products/wild_arugula.jpg',
            ],

            // Farmer 5 (Vegetables & Roots)
            [
                'farmer_profile_id' => $farmer5->id,
                'category_id'       => $catVeg->id,
                'name'              => 'Rainbow Swiss Chard',
                'description'       => 'Bright colorful stalks of tender nutrient-dense swiss chard.',
                'price'             => 3.00,
                'unit'              => 'bunch',
                'stock_quantity'    => 35,
                'weekly_quota'      => 50,
                'image_path'        => 'products/swiss_chard.jpg',
            ],
            [
                'farmer_profile_id' => $farmer5->id,
                'category_id'       => $catVeg->id,
                'name'              => 'Organic Golden Beets',
                'description'       => 'Mild, sweet earthy golden root beets with fresh edible green tops.',
                'price'             => 3.75,
                'unit'              => 'bunch',
                'stock_quantity'    => 40,
                'weekly_quota'      => 60,
                'image_path'        => 'products/golden_beets.jpg',
            ],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                [
                    'farmer_profile_id' => $p['farmer_profile_id'],
                    'name'              => $p['name'],
                ],
                [
                    'category_id'    => $p['category_id'],
                    'slug'           => Str::slug($p['name']) . '-' . uniqid(),
                    'description'    => $p['description'],
                    'price'          => $p['price'],
                    'unit'           => $p['unit'],
                    'stock_quantity' => $p['stock_quantity'],
                    'weekly_quota'   => $p['weekly_quota'],
                    'image_path'     => $p['image_path'],
                    'is_available'   => true,
                    'is_moderated'   => false,
                ]
            );
        }
    }
}
