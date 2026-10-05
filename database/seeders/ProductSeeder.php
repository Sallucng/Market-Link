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
                'image_url'         => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1556801712-76c8eb07bbc9?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1447175008436-054170c2e979?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1595152772835-219674b2a8a6?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1589985270826-4b7bb135bc9d?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1749655248287-d1e0acb5f8d1?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1764488034691-eda628fbdb7c?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1579113800032-c38bd7635818?auto=format&fit=crop&w=800&q=80',
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
                'image_url'         => 'https://images.unsplash.com/photo-1593105544559-ecb03bf76f82?auto=format&fit=crop&w=800&q=80',
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
                    'image_url'      => $p['image_url'],
                    'image_path'     => $p['image_url'],
                    'image'          => $p['image_url'],
                    'is_available'   => true,
                    'is_moderated'   => false,
                ]
            );
        }
    }
}
