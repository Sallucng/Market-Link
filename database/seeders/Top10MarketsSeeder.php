<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class Top10MarketsSeeder extends Seeder
{
    public function run(): void
    {
        // Update existing markets with high quality photos & descriptions
        $market1 = Market::find(1);
        if ($market1) {
            $market1->update([
                'name' => 'Downtown Farmers Plaza',
                'image_url' => 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=800&q=80',
                'description' => "Metropolis's premier weekend farmers market gathering over 20 regional growers, artisanal bakers, and organic family farms.",
            ]);
        }

        $market2 = Market::find(2);
        if ($market2) {
            $market2->update([
                'name' => 'Riverside Green and Artisan Market',
                'image_url' => 'https://images.unsplash.com/photo-1533900298318-6b8da08a523e?auto=format&fit=crop&w=800&q=80',
                'description' => "Scenic waterfront open-air pavilion showcasing fresh coastal orchards, apiaries, heirloom berries, and organic seasonal harvests.",
            ]);
        }

        $market3 = Market::find(3);
        if ($market3) {
            $market3->update([
                'name' => 'Oak Valley Community Harvest Fair',
                'image_url' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80',
                'description' => "A family-friendly Sunday harvest celebration in the heart of Oak Valley parkland with hydroponic greens, heritage poultry, and fresh dairy.",
            ]);
        }

        // Additional Markets to form the Top 10
        $marketsData = [
            [
                'name' => 'Highland Park Organic Exchange',
                'address' => '850 Highland Avenue, North Ridge',
                'city' => 'Metropolis',
                'operating_days' => 'Saturday',
                'timings' => '08:00 AM - 01:30 PM',
                'latitude' => 40.7380,
                'longitude' => -73.9850,
                'map_provider' => 'OpenStreetMap',
                'image_url' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=800&q=80',
                'description' => 'Dedicated 100% certified organic marketplace featuring cold-pressed juices, wild-foraged mushrooms, and sustainable regenerative root vegetables.',
            ],
            [
                'name' => 'Sunset Bay Harbor Market',
                'address' => '220 Marina Promenade, Sunset Bay',
                'city' => 'Waterfront',
                'operating_days' => 'Friday, Saturday',
                'timings' => '09:00 AM - 02:00 PM',
                'latitude' => 40.7180,
                'longitude' => -74.0200,
                'map_provider' => 'OpenStreetMap',
                'image_url' => 'https://images.unsplash.com/photo-1506484381205-f7945653044d?auto=format&fit=crop&w=800&q=80',
                'description' => 'Vibrant coastal market where maritime growers bring greenhouse greens, sea-salt roasted nuts, sourdough loaves, and freshly harvested tree fruits.',
            ],
            [
                'name' => 'Midtown Heritage Growers Shed',
                'address' => '610 Lexington Way, Historic District',
                'city' => 'Midtown',
                'operating_days' => 'Thursday, Sunday',
                'timings' => '08:30 AM - 02:30 PM',
                'latitude' => 40.7500,
                'longitude' => -73.9750,
                'map_provider' => 'OpenStreetMap',
                'image_url' => 'https://images.unsplash.com/photo-1516594798947-e65505dbb29d?auto=format&fit=crop&w=800&q=80',
                'description' => 'Historic covered shed hosting multi-generational family growers with antique apple varieties, artisan farmhouse cheeses, and seasonal preserves.',
            ],
            [
                'name' => 'Cedar Creek Agri-Market',
                'address' => '140 Cedar Valley Road',
                'city' => 'Cedar Valley',
                'operating_days' => 'Tuesday, Saturday',
                'timings' => '08:00 AM - 01:00 PM',
                'latitude' => 40.7050,
                'longitude' => -74.0150,
                'map_provider' => 'OpenStreetMap',
                'image_url' => 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=800&q=80',
                'description' => 'Nestled alongside Cedar Creek, this rustic market offers sun-grown stone fruits, free-range eggs, pasture-raised beef, and fragrant kitchen herbs.',
            ],
            [
                'name' => 'Pinecrest Eco Farmers Forum',
                'address' => '90 Forestview Boulevard',
                'city' => 'Pinecrest',
                'operating_days' => 'Saturday, Sunday',
                'timings' => '07:30 AM - 01:00 PM',
                'latitude' => 40.7300,
                'longitude' => -73.9650,
                'map_provider' => 'OpenStreetMap',
                'image_url' => 'https://images.unsplash.com/photo-1573246123716-6b1782bfc499?auto=format&fit=crop&w=800&q=80',
                'description' => 'Eco-forward zero-waste community market where local agroecologists share organic microgreens, cold-stored winter squash, and raw clover honey.',
            ],
            [
                'name' => 'Eastside Community Fresh Market',
                'address' => '340 East Boulevard, Green Commons',
                'city' => 'East Metropolis',
                'operating_days' => 'Wednesday, Sunday',
                'timings' => '09:00 AM - 03:00 PM',
                'latitude' => 40.7150,
                'longitude' => -73.9600,
                'map_provider' => 'OpenStreetMap',
                'image_url' => 'https://images.unsplash.com/photo-1526399232581-2ab5608b6336?auto=format&fit=crop&w=800&q=80',
                'description' => 'A bustling neighborhood hub with multicultural street produce, heirloom brassicas, heirloom tomatoes, and freshly pressed cider.',
            ],
            [
                'name' => 'Golden Ridge Harvest Commons',
                'address' => '1050 Foothill Parkway',
                'city' => 'Golden Ridge',
                'operating_days' => 'Sunday',
                'timings' => '08:00 AM - 02:00 PM',
                'latitude' => 40.7420,
                'longitude' => -74.0300,
                'map_provider' => 'OpenStreetMap',
                'image_url' => 'https://images.unsplash.com/photo-1471193945509-9ad0617afabf?auto=format&fit=crop&w=800&q=80',
                'description' => 'Picturesque valley overlook market featuring organic root vegetables, field pumpkins, sun-dried fruit, and fresh farm pantry staples.',
            ],
        ];

        $categories = Category::all();
        $vegCat = $categories->where('slug', 'vegetables')->first() ?? $categories->first();
        $fruitCat = $categories->where('slug', 'fruits')->first() ?? $categories->first();
        $honeyCat = $categories->where('slug', 'herbs-honey')->first() ?? $categories->first();

        $farmerTemplates = [
            [
                'username' => 'highland_growers',
                'name' => 'Clara Hensley',
                'stall_name' => 'Highland Valley Micro-Farm',
                'bio' => 'Regenerative grower focused on antioxidant-rich rainbow chard, purple carrots, and organic brassicas.',
                'image' => 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=600&q=80',
                'products' => [
                    ['name' => 'Rainbow Swiss Chard', 'price' => 3.75, 'unit' => 'bunch'],
                    ['name' => 'Organic Purple Carrots', 'price' => 4.20, 'unit' => 'lb'],
                ]
            ],
            [
                'username' => 'sunset_orchards',
                'name' => 'Mateo Silva',
                'stall_name' => 'Sunset Bay Citrus & Berries',
                'bio' => 'Coastal orchard yielding sweet golden raspberries, Meyer lemons, and cold-pressed citrus preserves.',
                'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
                'products' => [
                    ['name' => 'Meyer Lemons', 'price' => 5.00, 'unit' => 'bag'],
                    ['name' => 'Golden Raspberries', 'price' => 6.50, 'unit' => 'pint'],
                ]
            ],
            [
                'username' => 'midtown_cheese_co',
                'name' => 'Beatrice Dupont',
                'stall_name' => 'Heritage Pastures Dairy & Pantry',
                'bio' => 'Artisanal grass-fed goat cheese, cultured pasture butter, and farmstead organic yogurt.',
                'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
                'products' => [
                    ['name' => 'Farmstead Chèvre Goat Cheese', 'price' => 8.50, 'unit' => 'wheel'],
                    ['name' => 'Cultured Herb Butter', 'price' => 6.00, 'unit' => 'block'],
                ]
            ],
            [
                'username' => 'cedar_creek_roots',
                'name' => 'Lucas Sterling',
                'stall_name' => 'Cedar Creek Root & Herb Co.',
                'bio' => 'Hand-harvested culinary herbs, fresh ginger, horseradish, and heritage heirloom garlic varieties.',
                'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80',
                'products' => [
                    ['name' => 'Heirloom Hardneck Garlic', 'price' => 3.50, 'unit' => 'braid'],
                    ['name' => 'Fresh Rosemary & Thyme Bundle', 'price' => 2.80, 'unit' => 'bunch'],
                ]
            ],
            [
                'username' => 'pinecrest_apiaries',
                'name' => 'Hannah Lindqvist',
                'stall_name' => 'Pinecrest Forest Honey & Wax',
                'bio' => 'Treatment-free sustainable beekeeping delivering raw basswood honey, bee pollen, and beeswax food wraps.',
                'image' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=600&q=80',
                'products' => [
                    ['name' => 'Raw Forest Basswood Honey', 'price' => 12.00, 'unit' => 'jar'],
                    ['name' => 'Wildflower Comb Honey', 'price' => 14.50, 'unit' => 'box'],
                ]
            ],
            [
                'username' => 'eastside_greens',
                'name' => 'Tariq Al-Mansoor',
                'stall_name' => 'Eastside Urban Aquaponics',
                'bio' => 'Hyper-fresh living butterhead lettuce, micro basil, and crisp watercress harvested morning of market.',
                'image' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=600&q=80',
                'products' => [
                    ['name' => 'Living Butterhead Lettuce', 'price' => 3.25, 'unit' => 'head'],
                    ['name' => 'Genovese Micro Basil', 'price' => 4.50, 'unit' => 'clamshell'],
                ]
            ],
            [
                'username' => 'golden_ridge_farms',
                'name' => 'Evelyn Brooks',
                'stall_name' => 'Golden Ridge Heritage Farm',
                'bio' => 'Dry-farmed heirloom winter squash, pie pumpkins, crisp Asian pears, and freshly roasted squash seeds.',
                'image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
                'products' => [
                    ['name' => 'Honeynut Squash', 'price' => 2.90, 'unit' => 'lb'],
                    ['name' => 'Crisp Asian Pears', 'price' => 5.25, 'unit' => 'bag'],
                ]
            ],
        ];

        foreach ($marketsData as $index => $mData) {
            $createdMarket = Market::firstOrCreate(
                ['name' => $mData['name']],
                $mData
            );

            // Assign a unique farmer to this market if not already assigned
            if (isset($farmerTemplates[$index])) {
                $fData = $farmerTemplates[$index];
                $user = User::firstOrCreate(
                    ['username' => $fData['username']],
                    [
                        'name' => $fData['name'],
                        'email' => $fData['username'] . '@marketlink.local',
                        'contact_number' => '+1 (555) ' . rand(200, 899) . '-' . rand(1000, 9999),
                        'address' => $mData['address'],
                        'role' => 'farmer',
                        'is_active' => true,
                        'is_approved' => true,
                        'password' => Hash::make('Farmer@123'),
                    ]
                );

                $farmer = Farmer::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'market_id' => $createdMarket->id,
                        'stall_name' => $fData['stall_name'],
                        'contact_person' => $fData['name'],
                        'contact_number' => $user->contact_number,
                        'address' => 'Stall #' . rand(1, 24) . ', ' . $mData['name'],
                        'latitude' => $mData['latitude'] + 0.0002,
                        'longitude' => $mData['longitude'] + 0.0002,
                        'operating_days' => $mData['operating_days'],
                        'pickup_time_windows' => '09:00 AM - 11:30 AM, 12:00 PM - 02:00 PM',
                        'cutoff_hours' => 2,
                        'bio' => $fData['bio'],
                        'image_url' => $fData['image'],
                    ]
                );

                // Add products for this farmer
                foreach ($fData['products'] as $pData) {
                    Product::firstOrCreate(
                        ['farmer_id' => $farmer->id, 'name' => $pData['name']],
                        [
                            'category_id' => ($index % 2 === 0 ? $vegCat->id : ($index % 3 === 0 ? $honeyCat->id : $fruitCat->id)),
                            'description' => 'Freshly harvested, locally cultivated ' . strtolower($pData['name']) . ' from ' . $farmer->stall_name . '.',
                            'price' => $pData['price'],
                            'unit' => $pData['unit'],
                            'stock_quantity' => rand(20, 50),
                            'weekly_recurring_stock' => rand(25, 45),
                            'is_available' => true,
                            'image_url' => $fData['image'],
                        ]
                    );
                }
            }
        }
    }
}
