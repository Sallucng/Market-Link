<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FullSitePageReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $farmerUser;
    protected Farmer $farmerProfile;
    protected User $customerUser;
    protected Market $testMarket;
    protected Category $testCategory;
    protected Product $testProduct;
    protected Order $testOrder;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Market
        $this->testMarket = Market::create([
            'name' => 'Metro Plaza Farmers Market',
            'address' => '100 Central Square',
            'city' => 'Metropolis',
            'operating_days' => 'Saturday, Sunday',
            'timings' => '08:00 AM - 02:00 PM',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'image_url' => 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=800&q=80',
            'description' => 'A vibrant weekend farmers market.',
        ]);

        // 2. Create Category
        $this->testCategory = Category::create([
            'name' => 'Fresh Vegetables',
            'description' => 'Locally harvested organic vegetables',
        ]);

        // 3. Create Admin User
        $this->adminUser = User::create([
            'name' => 'Backoffice Admin',
            'username' => 'admin_test',
            'email' => 'admin@test.local',
            'contact_number' => '555-0100',
            'address' => 'Admin HQ',
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);

        // 4. Create Farmer User & Stall
        $this->farmerUser = User::create([
            'name' => 'Green Fields Farm',
            'username' => 'farmer_test',
            'email' => 'farmer@test.local',
            'contact_number' => '555-0200',
            'address' => 'Farmstead Way',
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);

        $this->farmerProfile = Farmer::create([
            'user_id' => $this->farmerUser->id,
            'market_id' => $this->testMarket->id,
            'stall_name' => 'Green Fields Stall',
            'bio' => 'Fresh seasonal organic produce.',
            'contact_person' => 'Farmer John',
            'contact_number' => '555-0200',
            'image_url' => 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=300&q=80',
        ]);

        // 5. Create Customer User
        $this->customerUser = User::create([
            'name' => 'Jane Shopper',
            'username' => 'customer_test',
            'email' => 'customer@test.local',
            'contact_number' => '555-0300',
            'address' => '12 Market St',
            'role' => 'customer',
            'is_active' => true,
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);

        // 6. Create Product
        $this->testProduct = Product::create([
            'farmer_id' => $this->farmerProfile->id,
            'category_id' => $this->testCategory->id,
            'name' => 'Crisp Organic Lettuce',
            'description' => 'Crisp organic butterhead lettuce harvested at dawn.',
            'price' => 3.50,
            'stock_quantity' => 45,
            'unit' => 'head',
            'image_url' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80',
            'is_active' => true,
            'is_approved' => true,
        ]);

        // 7. Create Sample Order
        $this->testOrder = Order::create([
            'order_number' => 'ORD-TEST-001',
            'customer_id' => $this->customerUser->id,
            'farmer_id' => $this->farmerProfile->id,
            'market_id' => $this->testMarket->id,
            'pickup_date' => now()->addDays(2)->format('Y-m-d'),
            'pickup_time_slot' => '08:00 AM - 10:00 AM',
            'total_amount' => 10.50,
            'order_status' => 'placed',
            'payment_method' => 'pay_at_pickup',
            'payment_status' => 'pending',
            'pickup_notes' => 'Will pickup at 10 AM',
        ]);

        OrderItem::create([
            'order_id' => $this->testOrder->id,
            'product_id' => $this->testProduct->id,
            'product_name' => $this->testProduct->name,
            'unit_price' => $this->testProduct->price,
            'quantity' => 3,
            'subtotal' => 10.50,
        ]);
    }

    /**
     * Group 1: Review All Public Storefront Pages
     */
    public function test_all_public_storefront_pages_render_cleanly(): void
    {
        $publicRoutes = [
            'Home Page' => route('home'),
            'About Page' => route('about'),
            'Contact Page' => route('contact'),
            'Markets Catalog' => route('markets.index'),
            'Market Detail' => route('markets.show', $this->testMarket->id),
            'Produce Catalog' => route('products.index'),
            'Product Detail' => route('products.show', $this->testProduct->id),
            'Farmer Stall Profile' => route('farmers.show', $this->farmerProfile->id),
            'Cart Page' => route('cart.index'),
            'Login Page' => route('login'),
            'Register Page' => route('register'),
        ];

        foreach ($publicRoutes as $name => $url) {
            $response = $this->get($url);
            $response->assertStatus(200, "Failed loading public page: {$name} ({$url})");
        }
    }

    /**
     * Group 2: Review All Customer Portal Pages
     */
    public function test_all_customer_portal_pages_render_cleanly(): void
    {
        $customerRoutes = [
            'Customer Dashboard' => route('customer.dashboard'),
            'My Orders History' => route('customer.orders.index'),
            'Order Detail View' => route('customer.orders.show', $this->testOrder->id),
            'Bookmarked Favorites' => route('customer.favorites.index'),
        ];

        foreach ($customerRoutes as $name => $url) {
            $response = $this->actingAs($this->customerUser)->get($url);
            $response->assertStatus(200, "Failed loading customer page: {$name} ({$url})");
        }
    }

    /**
     * Group 3: Review All Farmer Portal Pages
     */
    public function test_all_farmer_portal_pages_render_cleanly(): void
    {
        $farmerRoutes = [
            'Farmer Dashboard' => route('farmer.dashboard'),
            'Produce Inventory' => route('farmer.products.index'),
            'Add Produce View' => route('farmer.products.create'),
            'Edit Produce View' => route('farmer.products.edit', $this->testProduct->id),
            'Orders Pipeline Queue' => route('farmer.orders.index'),
            'Fulfillment Detail View' => route('farmer.orders.show', $this->testOrder->id),
            'Farmer Stall Profile' => route('farmer.profile'),
            'Customer Reviews & Responses' => route('farmer.reviews.index'),
        ];

        foreach ($farmerRoutes as $name => $url) {
            $response = $this->actingAs($this->farmerUser)->get($url);
            $response->assertStatus(200, "Failed loading farmer page: {$name} ({$url})");
        }
    }

    /**
     * Group 4: Review All Admin Portal Pages
     */
    public function test_all_admin_portal_pages_render_cleanly(): void
    {
        $adminRoutes = [
            'Admin Mission Control Dashboard' => route('admin.dashboard'),
            'Farmers Markets Manager' => route('admin.markets.index'),
            'Add Market View' => route('admin.markets.create'),
            'Edit Market View' => route('admin.markets.edit', $this->testMarket->id),
            'Categories Taxonomy' => route('admin.categories.index'),
            'Market Announcements' => route('admin.announcements.index'),
            'Produce Moderation' => route('admin.moderation.products'),
            'Review Moderation' => route('admin.moderation.reviews'),
            'Platform Reports & Analytics' => route('admin.reports.index'),
        ];

        foreach ($adminRoutes as $name => $url) {
            $response = $this->actingAs($this->adminUser)->get($url);
            $response->assertStatus(200, "Failed loading admin page: {$name} ({$url})");
        }
    }
}
