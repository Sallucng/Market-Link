<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'System Admin',
            'email' => 'admin@marketlink.com',
            'password' => bcrypt('AdminPass123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->adminToken = $this->adminUser->createToken('admin-test')->plainTextToken;
    }

    public function test_admin_authentication_and_dashboard(): void
    {
        $loginResponse = $this->postJson('/api/admin/login', [
            'email' => 'admin@marketlink.com',
            'password' => 'AdminPass123!',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['user', 'token']]);

        $dashboardResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/admin/dashboard');

        $dashboardResponse->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data' => [
                    'farmers' => ['total', 'approved', 'pending_approval', 'suspended'],
                    'customers' => ['total', 'active', 'inactive'],
                    'markets' => ['total', 'active'],
                    'orders' => ['total', 'pending', 'completed', 'cancelled', 'total_revenue'],
                ],
            ]);
    }

    public function test_admin_farmer_approval_lifecycle(): void
    {
        $farmer = User::create([
            'name' => 'New Farmer',
            'email' => 'new@farm.com',
            'password' => bcrypt('secret123'),
            'role' => 'farmer',
            'status' => 'pending',
        ]);

        $profile = $farmer->farmerProfile()->create([
            'farm_name' => 'New Valley Farm',
            'address' => '400 Valley Rd',
            'city' => 'Napa',
            'state' => 'CA',
            'postal_code' => '94558',
            'approval_status' => 'pending',
        ]);

        // 1. Approve
        $approveResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson("/api/admin/farmers/{$profile->id}/approve");

        $approveResponse->assertStatus(200)
            ->assertJsonPath('data.approval_status', 'approved')
            ->assertJsonPath('data.user.status', 'active');

        // 2. Suspend
        $suspendResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson("/api/admin/farmers/{$profile->id}/suspend", [
                'reason' => 'Violation of organic certification standards.',
            ]);

        $suspendResponse->assertStatus(200)
            ->assertJsonPath('data.is_approved', false)
            ->assertJsonPath('data.user.status', 'suspended');

        // 3. Reject
        $rejectResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson("/api/admin/farmers/{$profile->id}/reject", [
                'rejection_reason' => 'Incomplete documentation.',
            ]);

        $rejectResponse->assertStatus(200)
            ->assertJsonPath('data.is_approved', false)
            ->assertJsonPath('data.rejection_reason', 'Incomplete documentation.');
    }

    public function test_admin_market_crud(): void
    {
        // 1. Create
        $createResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/admin/markets', [
                'name' => 'Sunset Community Market',
                'description' => 'Evening neighborhood market.',
                'address' => '800 Sunset Blvd',
                'city' => 'San Francisco',
                'state' => 'CA',
                'postal_code' => '94122',
                'operating_days' => ['Thursday', 'Friday'],
                'opening_time' => '16:00',
                'closing_time' => '20:00',
            ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.name', 'Sunset Community Market');

        $marketId = $createResponse->json('data.id');

        // 2. Update
        $updateResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->putJson("/api/admin/markets/{$marketId}", [
                'name' => 'Sunset Twilight Market',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'Sunset Twilight Market');

        // 3. Delete
        $deleteResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->deleteJson("/api/admin/markets/{$marketId}");

        $deleteResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('markets', ['id' => $marketId]);
    }

    public function test_admin_moderation_and_announcements(): void
    {
        $farmer = User::create([
            'name' => 'Farmer Joe',
            'email' => 'joe@farm.com',
            'password' => bcrypt('password'),
            'role' => 'farmer',
            'status' => 'active',
        ]);

        $profile = $farmer->farmerProfile()->create([
            'farm_name' => 'Joe Farm',
            'address' => '1 Farm Rd',
            'city' => 'Fresno',
            'state' => 'CA',
            'postal_code' => '93720',
            'approval_status' => 'approved',
        ]);

        $category = Category::create([
            'name' => 'Herbs',
            'slug' => 'herbs',
        ]);

        $product = Product::create([
            'farmer_profile_id' => $profile->id,
            'category_id' => $category->id,
            'name' => 'Inappropriate Item',
            'slug' => 'inappropriate-item',
            'price' => 10.00,
            'unit' => 'piece',
            'stock_quantity' => 1,
            'weekly_stock' => 1,
            'status' => 'active',
        ]);

        // 1. Moderate Product (flag)
        $modProdResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson("/api/admin/moderation/products/{$product->id}", [
                'action' => 'flag',
                'reason' => 'Not an agricultural produce item.',
            ]);

        $modProdResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'flagged')
            ->assertJsonPath('data.is_available', false);

        // 2. Announcement CRUD
        $announcementResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/admin/announcements', [
                'title' => 'Severe Weather Notice',
                'message' => 'Saturday market postponed due to storm.',
                'target_audience' => 'farmer',
            ]);

        $announcementResponse->assertStatus(201)
            ->assertJsonPath('data.title', 'Severe Weather Notice')
            ->assertJsonPath('data.target_audience', 'farmer');

        $announcementId = $announcementResponse->json('data.id');

        $this->assertDatabaseHas('announcements', [
            'id' => $announcementId,
            'title' => 'Severe Weather Notice',
        ]);
    }

    public function test_admin_customer_management(): void
    {
        $customer = User::create([
            'name' => 'Alice Customer',
            'email' => 'alice@test.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        // List customers
        $listResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/admin/customers');

        $listResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');

        // Toggle status to suspended
        $toggleResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson("/api/admin/customers/{$customer->id}/status", [
                'status' => 'suspended',
            ]);

        $toggleResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'suspended');

        $this->assertEquals('suspended', $customer->fresh()->status);
    }

    public function test_admin_category_crud(): void
    {
        // 1. Create
        $createResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/admin/categories', [
                'name' => 'Organic Berries',
                'description' => 'Fresh seasonal berries',
                'is_active' => true,
            ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.name', 'Organic Berries');

        $categoryId = $createResponse->json('data.id');

        // 2. Update
        $updateResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->putJson("/api/admin/categories/{$categoryId}", [
                'name' => 'Wild & Organic Berries',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'Wild & Organic Berries');

        // 3. Delete
        $deleteResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->deleteJson("/api/admin/categories/{$categoryId}");

        $deleteResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
    }

    public function test_admin_review_moderation(): void
    {
        $farmer = User::create([
            'name' => 'Farmer Bob',
            'email' => 'bob@farm.com',
            'password' => bcrypt('password'),
            'role' => 'farmer',
            'status' => 'active',
        ]);

        $profile = $farmer->farmerProfile()->create([
            'business_name' => 'Bob Berry Farm',
            'address' => '100 Berry Lane',
            'is_approved' => true,
        ]);

        $customer = User::create([
            'name' => 'Reviewer Jane',
            'email' => 'jane@buyer.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'farmer_profile_id' => $profile->id,
            'total_amount' => 20.00,
            'status' => 'completed',
            'pickup_slot' => '10:00 AM - 11:00 AM',
        ]);

        $review = Review::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'farmer_profile_id' => $profile->id,
            'rating' => 1,
            'comment' => 'Spam content and abusive review.',
            'is_moderated' => false,
        ]);

        // Flag review
        $flagResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson("/api/admin/moderation/reviews/{$review->id}", [
                'action' => 'flag',
            ]);

        $flagResponse->assertStatus(200);
        $this->assertTrue((bool)$review->fresh()->is_moderated);

        // Show/Unflag review
        $showResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson("/api/admin/moderation/reviews/{$review->id}", [
                'action' => 'show',
            ]);

        $showResponse->assertStatus(200);
        $this->assertFalse((bool)$review->fresh()->is_moderated);
    }
}
