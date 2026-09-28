<?php

namespace Tests\Feature;

use App\Models\Farmer;
use App\Models\Market;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'System Admin',
            'username' => 'sysadmin',
            'email' => 'admin@test.local',
            'contact_number' => '555-9999',
            'address' => 'HQ',
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);
    }

    public function test_admin_can_view_dashboard_and_manage_markets(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Create market
        $marketResponse = $this->actingAs($this->adminUser)->post(route('admin.markets.store'), [
            'name' => 'Riverside Weekend Market',
            'address' => '200 River Rd',
            'city' => 'Brooklyn',
            'operating_days' => 'Sunday',
            'timings' => '09:00 AM - 02:00 PM',
            'latitude' => 40.7000,
            'longitude' => -73.9900,
        ]);

        $marketResponse->assertRedirect(route('admin.markets.index'));
        $this->assertDatabaseHas('markets', [
            'name' => 'Riverside Weekend Market',
        ]);
    }

    public function test_admin_farmer_approval_gate(): void
    {
        $unapprovedUser = User::create([
            'name' => 'New Farmer',
            'username' => 'newfarmer',
            'email' => 'newfarmer@test.local',
            'contact_number' => '555-5555',
            'address' => 'Farmland',
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => false,
            'password' => bcrypt('password'),
        ]);

        $farmer = Farmer::create([
            'user_id' => $unapprovedUser->id,
            'stall_name' => 'Pending Stall',
            'contact_person' => 'New Farmer',
            'contact_number' => '555-5555',
            'address' => 'Stall 9',
            'operating_days' => 'Sunday',
            'pickup_time_windows' => '09:00 AM - 11:00 AM',
            'cutoff_hours' => 2,
        ]);

        // Approve
        $approveResponse = $this->actingAs($this->adminUser)->post(route('admin.farmers.approve', $farmer->id));
        $approveResponse->assertRedirect();

        $unapprovedUser->refresh();
        $this->assertTrue((bool)$unapprovedUser->is_approved);
    }

    public function test_admin_dashboard_renders_analytics_charts_and_sidebar(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('marketRevenueBarChart');
        $response->assertSee('orderStatusPieChart');
        $response->assertSee('categoryDistributionChart');
        $response->assertSee('See as Normal User');
        $response->assertSee('admin-sidebar');
    }

    public function test_admin_can_view_reports_with_charts(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.index'));
        $response->assertStatus(200);
        $response->assertSee('reportMarketRevenueChart');
        $response->assertSee('reportOrderStatusChart');
        $response->assertSee('reportTopFarmersChart');
        $response->assertSee('See as Normal User');
        $response->assertSee('Generate CSV Report');
    }

    public function test_admin_can_export_csv_report(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.export'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_moderate_products_and_reviews(): void
    {
        $market = Market::create([
            'name' => 'City Market',
            'address' => '100 Main St',
            'city' => 'Metro',
            'operating_days' => 'Saturday',
            'timings' => '08:00 AM - 01:00 PM',
            'latitude' => 40.71,
            'longitude' => -74.00,
        ]);

        $farmerUser = User::create([
            'name' => 'Farmer Bob',
            'username' => 'farmerbob',
            'email' => 'bob@farmer.local',
            'role' => 'farmer',
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);

        $farmer = Farmer::create([
            'user_id' => $farmerUser->id,
            'market_id' => $market->id,
            'stall_name' => 'Bob Farms',
            'contact_person' => 'Bob',
            'contact_number' => '555-0001',
            'address' => 'Stall 1',
        ]);

        $cat = \App\Models\Category::create(['name' => 'Fresh Herbs']);

        $product = \App\Models\Product::create([
            'farmer_id' => $farmer->id,
            'category_id' => $cat->id,
            'name' => 'Questionable Product',
            'price' => 2.00,
            'stock_quantity' => 10,
            'unit' => 'bunch',
            'is_available' => true,
        ]);

        // Toggle delist
        $toggleRes = $this->actingAs($this->adminUser)->post(route('admin.moderation.products.toggle', $product->id));
        $toggleRes->assertRedirect();
        $this->assertFalse((bool)$product->fresh()->is_available);

        // Delete product
        $deleteRes = $this->actingAs($this->adminUser)->delete(route('admin.moderation.products.delete', $product->id));
        $deleteRes->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);

        // Moderate review
        $custUser = User::create([
            'name' => 'Spam Customer',
            'username' => 'spamcust',
            'email' => 'spam@test.local',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        $order = \App\Models\Order::create([
            'customer_id' => $custUser->id,
            'farmer_id' => $farmer->id,
            'market_id' => $market->id,
            'order_number' => 'ML-MOD-01',
            'order_status' => 'completed',
            'pickup_date' => now()->toDateString(),
            'pickup_time_slot' => '08:00 AM - 10:00 AM',
            'total_amount' => 5.00,
            'payment_method' => 'pay_at_pickup',
        ]);

        $review = \App\Models\Review::create([
            'order_id' => $order->id,
            'customer_id' => $custUser->id,
            'farmer_id' => $farmer->id,
            'rating' => 1,
            'comment' => 'Inappropriate review text violating policy',
        ]);

        $deleteRevRes = $this->actingAs($this->adminUser)->delete(route('admin.moderation.reviews.delete', $review->id));
        $deleteRevRes->assertRedirect();
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_admin_can_manage_categories_and_announcements(): void
    {
        // Category
        $catRes = $this->actingAs($this->adminUser)->post(route('admin.categories.store'), [
            'name' => 'Artisanal Baked Goods',
            'description' => 'Freshly baked breads and pastries',
            'icon' => 'bi-cake2',
        ]);
        $catRes->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Artisanal Baked Goods']);

        // Announcement
        $annRes = $this->actingAs($this->adminUser)->post(route('admin.announcements.store'), [
            'title' => 'Harvest Season Festival Next Week',
            'content' => 'Join us next Saturday for the seasonal pumpkin festival.',
            'badge_type' => 'info',
        ]);
        $annRes->assertRedirect();
        $this->assertDatabaseHas('announcements', ['title' => 'Harvest Season Festival Next Week']);
    }

    public function test_admin_can_toggle_customer_status(): void
    {
        $customer = User::create([
            'name' => 'Problem Customer',
            'username' => 'problemcust',
            'email' => 'problem@test.local',
            'role' => 'customer',
            'is_active' => true,
            'password' => bcrypt('password'),
        ]);

        $res = $this->actingAs($this->adminUser)->post(route('admin.customers.toggle', $customer->id));
        $res->assertRedirect();
        $this->assertFalse((bool)$customer->fresh()->is_active);

        $res2 = $this->actingAs($this->adminUser)->post(route('admin.customers.toggle', $customer->id));
        $res2->assertRedirect();
        $this->assertTrue((bool)$customer->fresh()->is_active);
    }

    public function test_admin_farmers_management_roster_and_filtering(): void
    {
        $fUser = User::create([
            'name' => 'Farmer Bob',
            'username' => 'farmerbob',
            'email' => 'bob@farms.local',
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);

        $farmer = Farmer::create([
            'user_id' => $fUser->id,
            'stall_name' => 'Bobs Organic Apples',
            'contact_person' => 'Farmer Bob',
            'contact_number' => '555-1234',
            'operating_days' => 'Saturday',
            'pickup_time_windows' => '08:00 AM - 12:00 PM',
            'cutoff_hours' => 2,
            'is_approved' => true,
            'approval_status' => 'approved',
        ]);

        $res = $this->actingAs($this->adminUser)->get(route('admin.farmers.index'));
        $res->assertStatus(200);
        $res->assertSee('Bobs Organic Apples');
        $res->assertSee('Farmer Stalls');

        // Suspend farmer
        $suspendRes = $this->actingAs($this->adminUser)->post(route('admin.farmers.suspend', $farmer->id), [
            'reason' => 'Annual compliance check',
        ]);
        $suspendRes->assertRedirect();
        $farmer->refresh();
        $this->assertEquals('suspended', $farmer->approval_status);
        $this->assertFalse((bool)$farmer->is_approved);
        $this->assertFalse((bool)$farmer->user->is_active);

        // Reinstate farmer
        $reinstateRes = $this->actingAs($this->adminUser)->post(route('admin.farmers.reinstate', $farmer->id));
        $reinstateRes->assertRedirect();
        $farmer->refresh();
        $this->assertEquals('approved', $farmer->approval_status);
        $this->assertTrue((bool)$farmer->is_approved);
        $this->assertTrue((bool)$farmer->user->is_active);
    }

    public function test_admin_customers_management_roster(): void
    {
        $customer = User::create([
            'name' => 'Alice Shopper',
            'username' => 'aliceshopper',
            'email' => 'alice@shopper.local',
            'role' => 'customer',
            'is_active' => true,
            'password' => bcrypt('password'),
        ]);

        $res = $this->actingAs($this->adminUser)->get(route('admin.customers.index'));
        $res->assertStatus(200);
        $res->assertSee('Alice Shopper');
        $res->assertSee('Customer Accounts');
    }
}
