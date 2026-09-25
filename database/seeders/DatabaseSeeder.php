<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Mandatory Test Users (SRS Section 1.9)
        $admin = User::create([
            'username' => 'admin',
            'name' => 'System Administrator',
            'email' => 'admin@marketlink.local',
            'contact_number' => '+1 (555) 019-2831',
            'address' => 'MarketLink HQ, Suite 400',
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
            'password' => Hash::make('Admin@123'),
        ]);

        $farmerUser1 = User::create([
            'username' => 'greenvalley',
            'name' => 'Johnathan Green',
            'email' => 'farmer@marketlink.local',
            'contact_number' => '+1 (555) 349-1122',
            'address' => 'Green Valley Farm, Route 9',
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => true,
            'password' => Hash::make('Farmer@123'),
        ]);

        $farmerUser2 = User::create([
            'username' => 'sunshineorchard',
            'name' => 'Elena Rodriguez',
            'email' => 'orchard@marketlink.local',
            'contact_number' => '+1 (555) 887-4321',
            'address' => '142 Orchard Lane, Hill Country',
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => true,
            'password' => Hash::make('Farmer@123'),
        ]);

        $farmerUser3 = User::create([
            'username' => 'newharvest',
            'name' => 'Marcus Vance',
            'email' => 'newharvest@marketlink.local',
            'contact_number' => '+1 (555) 672-9900',
            'address' => '88 Greenway Shed, South District',
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => false, // Pending Admin Approval
            'password' => Hash::make('Farmer@123'),
        ]);

        $customer1 = User::create([
            'username' => 'sarah_shopper',
            'name' => 'Sarah Shopper',
            'email' => 'customer@marketlink.local',
            'contact_number' => '+1 (555) 234-5678',
            'address' => '742 Evergreen Terrace, Apt 4B',
            'role' => 'customer',
            'is_active' => true,
            'is_approved' => true,
            'password' => Hash::make('Customer@123'),
        ]);

        $customer2 = User::create([
            'username' => 'david_miller',
            'name' => 'David Miller',
            'email' => 'david@marketlink.local',
            'contact_number' => '+1 (555) 987-6543',
            'address' => '12 Maplewood Drive',
            'role' => 'customer',
            'is_active' => true,
            'is_approved' => true,
            'password' => Hash::make('Customer@123'),
        ]);

        // 2. Markets (with real geographic coordinates for OpenStreetMap)
        $market1 = Market::create([
            'name' => "Downtown Farmers Plaza",
            'address' => '100 Central Square, Downtown',
            'city' => 'Metropolis',
            'operating_days' => 'Saturday, Sunday',
            'timings' => '08:00 AM - 02:00 PM',
            'latitude' => 40.712776,
            'longitude' => -74.005974,
            'map_provider' => 'OpenStreetMap',
        ]);

        $market2 = Market::create([
            'name' => "Riverside Green & Artisan Market",
            'address' => '450 Harbor Boulevard, Waterfront',
            'city' => 'Metropolis',
            'operating_days' => 'Wednesday, Saturday',
            'timings' => '09:00 AM - 03:00 PM',
            'latitude' => 40.725800,
            'longitude' => -74.011200,
            'map_provider' => 'OpenStreetMap',
        ]);

        $market3 = Market::create([
            'name' => "Oak Valley Community Harvest Fair",
            'address' => '1200 Parkside Avenue, Oak Valley',
            'city' => 'Metropolis',
            'operating_days' => 'Sunday',
            'timings' => '07:30 AM - 01:30 PM',
            'latitude' => 40.702000,
            'longitude' => -73.992000,
            'map_provider' => 'OpenStreetMap',
        ]);

        // 3. Farmer Profiles
        $farmer1 = Farmer::create([
            'user_id' => $farmerUser1->id,
            'market_id' => $market1->id,
            'stall_name' => 'Green Valley Organic Produce',
            'contact_person' => 'Johnathan Green',
            'contact_number' => '+1 (555) 349-1122',
            'address' => 'Stall #12, North Corridor, Downtown Plaza',
            'latitude' => 40.712950,
            'longitude' => -74.005850,
            'operating_days' => 'Saturday, Sunday',
            'pickup_time_windows' => '08:30 AM - 10:30 AM, 11:00 AM - 01:00 PM',
            'cutoff_hours' => 3,
            'bio' => 'Family-owned certified sustainable produce farm cultivating heirloom tomatoes, crunchy leafy greens, and root vegetables fresh from the soil.',
            'image_url' => 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=600&q=80',
        ]);

        $farmer2 = Farmer::create([
            'user_id' => $farmerUser2->id,
            'market_id' => $market2->id,
            'stall_name' => 'Sunshine Orchards & Wildflower Apiary',
            'contact_person' => 'Elena Rodriguez',
            'contact_number' => '+1 (555) 887-4321',
            'address' => 'Pier 4 Canopy Stall #4B, Riverside',
            'latitude' => 40.725920,
            'longitude' => -74.011050,
            'operating_days' => 'Wednesday, Saturday',
            'pickup_time_windows' => '09:30 AM - 11:30 AM, 12:00 PM - 02:00 PM',
            'cutoff_hours' => 2,
            'bio' => 'Heritage orchard specializing in crisp seasonal apples, sun-ripened berries, pure raw wildflower honey, and fresh-pressed cider.',
            'image_url' => 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?auto=format&fit=crop&w=600&q=80',
        ]);

        $farmer3 = Farmer::create([
            'user_id' => $farmerUser3->id,
            'market_id' => $market3->id,
            'stall_name' => 'New Harvest Urban Greens',
            'contact_person' => 'Marcus Vance',
            'contact_number' => '+1 (555) 672-9900',
            'address' => 'Pending Stall Allocation',
            'latitude' => 40.702100,
            'longitude' => -73.991900,
            'operating_days' => 'Sunday',
            'pickup_time_windows' => '08:00 AM - 10:00 AM',
            'cutoff_hours' => 2,
            'bio' => 'Urban micro-farm producing nutrient-dense microgreens, edible flowers, and artisanal culinary herbs.',
            'image_url' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80',
        ]);

        // 4. Product Categories (SRS §1.6)
        $catVeg = Category::create([
            'name' => 'Fresh Vegetables',
            'slug' => 'vegetables',
            'description' => 'Crisp, garden-picked leafy greens, roots, tomatoes, and seasonal vegetables.',
            'icon' => 'bi-carrot',
        ]);

        $catFruit = Category::create([
            'name' => 'Orchard Fruits',
            'slug' => 'fruits',
            'description' => 'Tree-ripened seasonal apples, berries, peaches, and citrus.',
            'icon' => 'bi-apple',
        ]);

        $catDairy = Category::create([
            'name' => 'Farm Fresh Dairy & Eggs',
            'slug' => 'dairy-eggs',
            'description' => 'Free-range pasture-raised eggs, artisanal cheese, and raw butter.',
            'icon' => 'bi-egg-fried',
        ]);

        $catBaked = Category::create([
            'name' => 'Artisanal Baked Goods',
            'slug' => 'baked-goods',
            'description' => 'Stone-milled sourdough loaves, rustic baguettes, and country pies.',
            'icon' => 'bi-basket',
        ]);

        $catHerbs = Category::create([
            'name' => 'Herbs & Farm Honey',
            'slug' => 'herbs-honey',
            'description' => 'Aromatic kitchen herbs, teas, and unpasteurized raw honey.',
            'icon' => 'bi-flower1',
        ]);

        // 5. Products for Farmer 1
        $p1 = Product::create([
            'farmer_id' => $farmer1->id,
            'category_id' => $catVeg->id,
            'name' => 'Heirloom Vine Tomatoes',
            'description' => 'Sweet, juicy multi-colored heirloom tomatoes harvested at peak ripeness.',
            'price' => 4.50,
            'unit' => 'kg',
            'stock_quantity' => 35,
            'weekly_recurring_stock' => 50,
            'is_sold_out' => false,
            'is_available' => true,
            'image_url' => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=600&q=80',
        ]);

        $p2 = Product::create([
            'farmer_id' => $farmer1->id,
            'category_id' => $catVeg->id,
            'name' => 'Organic Tuscan Kale & Chard Bundle',
            'description' => 'Deep green, pesticide-free kale bundled fresh with rainbow chard.',
            'price' => 3.25,
            'unit' => 'bunch',
            'stock_quantity' => 20,
            'weekly_recurring_stock' => 30,
            'is_sold_out' => false,
            'is_available' => true,
            'image_url' => 'https://images.unsplash.com/photo-1524179091875-bf99a9a6fa57?auto=format&fit=crop&w=600&q=80',
        ]);

        $p3 = Product::create([
            'farmer_id' => $farmer1->id,
            'category_id' => $catVeg->id,
            'name' => 'Sweet Japanese Sweet Potatoes',
            'description' => 'Creamy, purple-skinned white-fleshed sweet potatoes freshly dug.',
            'price' => 3.80,
            'unit' => 'kg',
            'stock_quantity' => 40,
            'weekly_recurring_stock' => 40,
            'is_sold_out' => false,
            'is_available' => true,
            'image_url' => 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?auto=format&fit=crop&w=600&q=80',
        ]);

        $p4 = Product::create([
            'farmer_id' => $farmer1->id,
            'category_id' => $catDairy->id,
            'name' => 'Pasture-Raised Organic Brown Eggs',
            'description' => 'Rich golden yolks from foraging, free-roaming Rhode Island hens.',
            'price' => 6.00,
            'unit' => 'dozen',
            'stock_quantity' => 15,
            'weekly_recurring_stock' => 25,
            'is_sold_out' => false,
            'is_available' => true,
            'image_url' => 'https://images.unsplash.com/photo-1516467508483-a7212febe31a?auto=format&fit=crop&w=600&q=80',
        ]);

        // Products for Farmer 2
        $p5 = Product::create([
            'farmer_id' => $farmer2->id,
            'category_id' => $catFruit->id,
            'name' => 'Honeycrisp Crisp Apples',
            'description' => 'Extra crunchy and explosive sweetness, orchard-picked yesterday.',
            'price' => 4.90,
            'unit' => 'kg',
            'stock_quantity' => 50,
            'weekly_recurring_stock' => 60,
            'is_sold_out' => false,
            'is_available' => true,
            'image_url' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=600&q=80',
        ]);

        $p6 = Product::create([
            'farmer_id' => $farmer2->id,
            'category_id' => $catHerbs->id,
            'name' => 'Raw Wildflower Honeycomb Jar',
            'description' => 'Pure unpasteurized honey bottled straight from our riverside hives.',
            'price' => 9.50,
            'unit' => 'jar (500g)',
            'stock_quantity' => 18,
            'weekly_recurring_stock' => 20,
            'is_sold_out' => false,
            'is_available' => true,
            'image_url' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&w=600&q=80',
        ]);

        $p7 = Product::create([
            'farmer_id' => $farmer2->id,
            'category_id' => $catBaked->id,
            'name' => 'Rustic Country Sourdough Batard',
            'description' => 'Naturally fermented 36-hour sourdough with deep caramel blistered crust.',
            'price' => 6.50,
            'unit' => 'loaf',
            'stock_quantity' => 12,
            'weekly_recurring_stock' => 15,
            'is_sold_out' => false,
            'is_available' => true,
            'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=600&q=80',
        ]);

        // 6. Sample Pre-Orders (Pay-at-Pickup)
        $order1 = Order::create([
            'customer_id' => $customer1->id,
            'farmer_id' => $farmer1->id,
            'market_id' => $market1->id,
            'order_number' => 'ML-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'order_status' => 'completed',
            'pickup_date' => now()->subDays(3)->toDateString(),
            'pickup_time_slot' => '09:00 AM - 10:00 AM',
            'total_amount' => 15.25,
            'payment_method' => 'pay_at_pickup',
            'cutoff_time' => now()->subDays(3)->setTime(6, 0),
            'notes' => 'Please include extra ripe tomatoes if possible.',
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $p1->id,
            'quantity' => 2,
            'unit_price' => 4.50,
            'subtotal' => 9.00,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $p2->id,
            'quantity' => 1,
            'unit_price' => 3.25,
            'subtotal' => 3.25,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $p3->id,
            'quantity' => 1,
            'unit_price' => 3.80,
            'subtotal' => 3.80,
        ]);

        // Completed Order Review (SRS §1.6)
        Review::create([
            'order_id' => $order1->id,
            'customer_id' => $customer1->id,
            'farmer_id' => $farmer1->id,
            'product_id' => $p1->id,
            'rating' => 5,
            'comment' => 'The heirloom tomatoes were the sweetest I have ever tasted! Pickup at the stall was super smooth.',
            'farmer_response' => 'Thank you so much Sarah! Glad you loved this week’s harvest.',
            'responded_at' => now()->subDays(2),
        ]);

        // Order 2: Ready for Pickup
        $order2 = Order::create([
            'customer_id' => $customer1->id,
            'farmer_id' => $farmer2->id,
            'market_id' => $market2->id,
            'order_number' => 'ML-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'order_status' => 'ready_for_pickup',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time_slot' => '10:00 AM - 11:30 AM',
            'total_amount' => 19.30,
            'payment_method' => 'pay_at_pickup',
            'cutoff_time' => now()->addDay()->setTime(7, 30),
            'notes' => 'I will be coming around 10:15 AM.',
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $p5->id,
            'quantity' => 2,
            'unit_price' => 4.90,
            'subtotal' => 9.80,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $p6->id,
            'quantity' => 1,
            'unit_price' => 9.50,
            'subtotal' => 9.50,
        ]);

        // Notifications
        Notification::create([
            'user_id' => $customer1->id,
            'title' => 'Pre-Order Ready for Pickup!',
            'message' => 'Your pre-order #' . $order2->order_number . ' is packed and ready for pickup at Sunshine Orchards stall in Riverside Market.',
            'is_read' => false,
            'type' => 'order',
        ]);

        // System Announcement (SRS §1.6)
        Announcement::create([
            'created_by' => $admin->id,
            'title' => 'Welcome to MarketLink — Local Harvest Pre-Order Platform',
            'content' => 'Support local growers, reserve fresh harvest in advance, and pick up directly at your neighborhood market stalls. Remember: all pre-orders are settled in person at pickup.',
            'badge_type' => 'success',
            'is_active' => true,
        ]);
    }
}
