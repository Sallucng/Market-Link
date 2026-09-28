<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class Market1FarmersSeeder extends Seeder
{
    public function run(): void
    {
        $market = Market::find(1);
        if (!$market) {
            $market = Market::first();
        }

        if (!$market) {
            return;
        }

        $vegCat = Category::where('slug', 'vegetables')->first() ?: Category::first();
        $fruitCat = Category::where('slug', 'fruits')->first() ?: $vegCat;
        $dairyCat = Category::where('slug', 'dairy-eggs')->first() ?: $vegCat;
        $bakedCat = Category::where('slug', 'baked-goods')->first() ?: $vegCat;
        $herbsCat = Category::where('slug', 'herbs-honey')->first() ?: $vegCat;

        $farmersData = [
            [
                'username' => 'hassan_rana',
                'name' => 'Hassan Rana',
                'email' => 'hassan@marketlink.local',
                'contact_number' => '+1 (555) 742-8819',
                'stall_name' => "Hassan's Heritage Orchards & Berries",
                'address' => 'Stall #14, East Pavilion, ' . $market->name,
                'latitude' => $market->latitude + 0.0003,
                'longitude' => $market->longitude - 0.0002,
                'operating_days' => 'Saturday, Sunday',
                'pickup_time_windows' => '08:30 AM - 10:30 AM, 11:30 AM - 01:30 PM',
                'cutoff_hours' => 2,
                'bio' => 'Third-generation fruit grower dedicated to organic heirloom orchard fruits, sun-drenched blackberries, crisp Gala apples, and cold-pressed unfiltered orchard juices. Committed to natural biodiversity and low-till practices.',
                'image_url' => '/images/farmers/farmer-1.webp',
                'products' => [
                    [
                        'name' => 'Crisp Royal Gala Apples',
                        'category_id' => $fruitCat->id,
                        'description' => 'Crisp, sweet, and aromatic orchard apples harvested fresh at daybreak.',
                        'price' => 4.20,
                        'unit' => 'kg',
                        'stock' => 45,
                        'image' => '/images/products/gala-apples.jpg',
                    ],
                    [
                        'name' => 'Wild Mountain Blackberries',
                        'category_id' => $fruitCat->id,
                        'description' => 'Plump, deeply sweet organic blackberries picked at peak ripeness.',
                        'price' => 5.50,
                        'unit' => 'punnet (300g)',
                        'stock' => 25,
                        'image' => '/images/products/blackberries.jpg',
                    ],
                    [
                        'name' => 'Cold-Pressed Unfiltered Apple Cider',
                        'category_id' => $fruitCat->id,
                        'description' => 'Pure orchard cider pressed with zero added sugar or preservatives.',
                        'price' => 6.00,
                        'unit' => 'bottle (1L)',
                        'stock' => 30,
                        'image' => '/images/products/apple-cider.jpg',
                    ],
                ],
            ],
            [
                'username' => 'sarah_bakery',
                'name' => 'Sarah Jenkins',
                'email' => 'sarah.jenkins@marketlink.local',
                'contact_number' => '+1 (555) 412-9022',
                'stall_name' => 'Artisan Hearth Bakery & Mill',
                'address' => 'Stall #16, North Arcade, ' . $market->name,
                'latitude' => $market->latitude - 0.0002,
                'longitude' => $market->longitude + 0.0004,
                'operating_days' => 'Saturday, Sunday',
                'pickup_time_windows' => '08:00 AM - 11:00 AM, 12:00 PM - 02:00 PM',
                'cutoff_hours' => 3,
                'bio' => 'Wood-fired sourdough baker using stone-ground heritage grains sourced directly from regional partner mills. Naturally leavened, long-fermented loaves with deep flavor crusts and open airy crumb.',
                'image_url' => '/images/farmers/farmer-10.jpg',
                'products' => [
                    [
                        'name' => 'Country Seeded Sourdough Loaf',
                        'category_id' => $bakedCat->id,
                        'description' => '36-hour slow fermented sourdough coated with toasted sesame, poppy, and flax seeds.',
                        'price' => 7.00,
                        'unit' => 'loaf',
                        'stock' => 20,
                        'image' => '/images/products/sourdough-bread-2.jpg',
                    ],
                    [
                        'name' => 'Rustic Olive & Rosemary Focaccia',
                        'category_id' => $bakedCat->id,
                        'description' => 'Crisp outside, pillowy inside focaccia with Kalamata olives and garden rosemary.',
                        'price' => 5.50,
                        'unit' => 'slab',
                        'stock' => 18,
                        'image' => '/images/products/millet-pack.webp',
                    ],
                ],
            ],
            [
                'username' => 'liam_dairy',
                'name' => 'Liam MacIntyre',
                'email' => 'liam.macintyre@marketlink.local',
                'contact_number' => '+1 (555) 631-4820',
                'stall_name' => 'Pasture Gold Dairy & Creamery',
                'address' => 'Stall #18, Central Square, ' . $market->name,
                'latitude' => $market->latitude + 0.0001,
                'longitude' => $market->longitude + 0.0005,
                'operating_days' => 'Saturday, Sunday',
                'pickup_time_windows' => '08:30 AM - 11:30 AM, 12:30 PM - 02:00 PM',
                'cutoff_hours' => 2,
                'bio' => 'Grass-fed dairy farm where Jersey cows graze on lush coastal pastures. We craft small-batch aged raw milk cheddar, cultured salted butter, and gather farm-fresh pasture eggs every morning.',
                'image_url' => '/images/farmers/farmer-2.jpg',
                'products' => [
                    [
                        'name' => 'Raw Milk Farmhouse Cheddar (Aged 12 Mo)',
                        'category_id' => $dairyCat->id,
                        'description' => 'Sharp, crumbly, and nutty raw grass-fed milk cheddar aged in our cellar.',
                        'price' => 8.50,
                        'unit' => 'block (250g)',
                        'stock' => 22,
                        'image' => 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'name' => 'Cultured Golden Sea-Salt Butter',
                        'category_id' => $dairyCat->id,
                        'description' => 'Traditional churned high-fat cultured butter with flaked Atlantic sea salt.',
                        'price' => 5.75,
                        'unit' => 'roll (200g)',
                        'stock' => 30,
                        'image' => 'https://images.unsplash.com/photo-1589985270826-4b7bb135bc9d?auto=format&fit=crop&w=600&q=80',
                    ],
                ],
            ],
            [
                'username' => 'amina_herbs',
                'name' => 'Amina Chen',
                'email' => 'amina.chen@marketlink.local',
                'contact_number' => '+1 (555) 890-3412',
                'stall_name' => 'Silk Road Culinary Herbs & Honey',
                'address' => 'Stall #20, South Garden, ' . $market->name,
                'latitude' => $market->latitude - 0.0004,
                'longitude' => $market->longitude - 0.0003,
                'operating_days' => 'Saturday, Sunday',
                'pickup_time_windows' => '09:00 AM - 12:00 PM, 12:30 PM - 02:00 PM',
                'cutoff_hours' => 2,
                'bio' => 'Specializing in fragrant fresh culinary herbs, medicinal botanical teas, and small-batch raw wildflower honey. We grow using companion planting without synthetic pesticides or chemicals.',
                'image_url' => '/images/farmers/farmer-5.jpg',
                'products' => [
                    [
                        'name' => 'Raw Blossom Honeycomb Hexagon',
                        'category_id' => $herbsCat->id,
                        'description' => '100% natural comb cut straight from rooftop hives, rich in wildflower pollen.',
                        'price' => 11.00,
                        'unit' => 'jar (450g)',
                        'stock' => 16,
                        'image' => 'https://images.unsplash.com/photo-1558642452-9d2a7deb7f62?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'name' => 'Culinary Fresh Herb Bouquet',
                        'category_id' => $herbsCat->id,
                        'description' => 'Freshly cut bunch of Italian rosemary, lemon thyme, Greek oregano, and sage.',
                        'price' => 3.90,
                        'unit' => 'bouquet',
                        'stock' => 25,
                        'image' => 'https://images.unsplash.com/photo-1515543237350-b3eea1ec8082?auto=format&fit=crop&w=600&q=80',
                    ],
                ],
            ],
        ];

        foreach ($farmersData as $fData) {
            $user = User::firstOrCreate(
                ['username' => $fData['username']],
                [
                    'name' => $fData['name'],
                    'email' => $fData['email'],
                    'contact_number' => $fData['contact_number'],
                    'address' => $fData['address'],
                    'role' => 'farmer',
                    'is_active' => true,
                    'is_approved' => true,
                    'password' => Hash::make('Farmer@123'),
                ]
            );

            // Ensure approved and active
            $user->update([
                'is_approved' => true,
                'is_active' => true,
            ]);

            $farmer = Farmer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'market_id' => $market->id,
                    'stall_name' => $fData['stall_name'],
                    'contact_person' => $fData['name'],
                    'contact_number' => $fData['contact_number'],
                    'address' => $fData['address'],
                    'latitude' => $fData['latitude'],
                    'longitude' => $fData['longitude'],
                    'operating_days' => $fData['operating_days'],
                    'pickup_time_windows' => $fData['pickup_time_windows'],
                    'cutoff_hours' => $fData['cutoff_hours'],
                    'bio' => $fData['bio'],
                    'image_url' => $fData['image_url'],
                ]
            );

            foreach ($fData['products'] as $pData) {
                Product::updateOrCreate(
                    ['farmer_id' => $farmer->id, 'name' => $pData['name']],
                    [
                        'category_id' => $pData['category_id'],
                        'description' => $pData['description'],
                        'price' => $pData['price'],
                        'unit' => $pData['unit'],
                        'stock_quantity' => $pData['stock'],
                        'weekly_recurring_stock' => $pData['stock'] + 10,
                        'is_available' => true,
                        'is_sold_out' => false,
                        'image_url' => $pData['image'],
                    ]
                );
            }
        }
    }
}
