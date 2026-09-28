<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_all_modules_in_strict_order(): void
    {
        $this->seed(DatabaseSeeder::class);

        // 1. Verify Admin User
        $admin = User::where('role', 'admin')->where('email', 'admin@marketlink.com')->first();
        $this->assertNotNull($admin);
        $this->assertEquals('active', $admin->status);

        // 2. Verify Agricultural Categories
        $this->assertEquals(5, Category::count());
        $this->assertDatabaseHas('categories', ['name' => 'Vegetables']);
        $this->assertDatabaseHas('categories', ['name' => 'Fruits']);
        $this->assertDatabaseHas('categories', ['name' => 'Dairy']);
        $this->assertDatabaseHas('categories', ['name' => 'Poultry']);
        $this->assertDatabaseHas('categories', ['name' => 'Organic Herbs']);

        // 3. Verify Markets
        $this->assertGreaterThanOrEqual(3, Market::count());
        $this->assertDatabaseHas('markets', ['name' => 'Greenfield Central Farmers Market']);

        // 4. Verify Farmers (5 approved, 2 pending)
        $this->assertEquals(5, FarmerProfile::where('is_approved', true)->count());
        $this->assertEquals(2, FarmerProfile::where('is_approved', false)->count());
        $this->assertEquals(7, User::where('role', 'farmer')->count());

        // Verify Market Pivot Associations
        $approvedFarmer = FarmerProfile::where('is_approved', true)->first();
        $this->assertNotEmpty($approvedFarmer->markets);

        // 5. Verify Products
        $this->assertEquals(15, Product::count());
        $this->assertDatabaseHas('products', ['name' => 'Organic Heirloom Tomatoes']);

        // 6. Verify Orders & Order Items
        $this->assertGreaterThanOrEqual(5, Order::count());
        $this->assertDatabaseHas('orders', ['status' => 'pending']);
        $this->assertDatabaseHas('orders', ['status' => 'accepted']);
        $this->assertDatabaseHas('orders', ['status' => 'ready_for_pickup']);
        $this->assertDatabaseHas('orders', ['status' => 'completed']);
        $this->assertGreaterThanOrEqual(5, OrderItem::count());

        // 7. Verify Reviews
        $this->assertGreaterThanOrEqual(1, Review::count());
        $reviewWithReply = Review::whereNotNull('farmer_reply')->first();
        $this->assertNotNull($reviewWithReply);
        $this->assertGreaterThanOrEqual(4, $reviewWithReply->rating);

        // 8. Verify Announcements
        $this->assertGreaterThanOrEqual(4, Announcement::count());
        $this->assertDatabaseHas('announcements', ['target_role' => 'all']);
        $this->assertDatabaseHas('announcements', ['target_role' => 'farmer']);
        $this->assertDatabaseHas('announcements', ['target_role' => 'customer']);
    }
}
