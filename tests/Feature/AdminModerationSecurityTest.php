<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModerationSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private string $adminToken;
    private User $farmerUser;
    private FarmerProfile $farmerProfile;
    private string $farmerToken;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name'     => 'Security Admin',
            'email'    => 'admin@marketlink.com',
            'password' => bcrypt('AdminSecret123!'),
            'role'     => 'admin',
            'status'   => 'active',
        ]);
        $this->adminToken = $this->adminUser->createToken('admin-token')->plainTextToken;

        $this->farmerUser = User::create([
            'name'     => 'Farmer Under Review',
            'email'    => 'farmer.review@farm.com',
            'password' => bcrypt('FarmerSecret123!'),
            'role'     => 'farmer',
            'status'   => 'active',
        ]);

        $this->farmerProfile = $this->farmerUser->farmerProfile()->create([
            'business_name'     => 'Green Meadows Produce',
            'stall_number'      => 'Stall #12',
            'address'           => '500 Valley Road',
            'operating_days'    => ['Saturday', 'Sunday'],
            'pickup_start_time' => '08:00',
            'pickup_end_time'   => '13:00',
            'is_approved'       => true,
        ]);

        $this->farmerToken = $this->farmerUser->createToken('farmer-active-token')->plainTextToken;

        $this->category = Category::create([
            'name'      => 'Vegetables',
            'slug'      => 'vegetables',
            'is_active' => true,
        ]);
    }

    /**
     * 1. Suspending a farmer account automatically revokes active tokens and blocks access.
     */
    public function test_suspending_farmer_account_revokes_tokens_and_blocks_access(): void
    {
        // Confirm farmer can access endpoint with active token
        $initialResponse = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->getJson('/api/v1/farmer/profile');
        $initialResponse->assertStatus(200);

        // Reset auth guards to switch user context from farmer to admin
        auth()->forgetGuards();

        // Admin suspends the farmer account
        $suspendResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/admin/farmers/{$this->farmerProfile->id}/suspend", [
                'reason' => 'Violation of platform vendor terms of service.',
            ]);

        $suspendResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');

        // Verify database state: is_approved = false, status = suspended
        $this->assertDatabaseHas('farmer_profiles', [
            'id'          => $this->farmerProfile->id,
            'is_approved' => false,
        ]);
        $this->assertDatabaseHas('users', [
            'id'     => $this->farmerUser->id,
            'status' => 'suspended',
        ]);

        // Reset auth guards so Sanctum re-evaluates the farmer's revoked Bearer token
        auth()->forgetGuards();

        // Attempting to access farmer endpoints with previous token must fail with 401 Unauthenticated
        $blockedResponse = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->getJson('/api/v1/farmer/profile');

        $blockedResponse->assertStatus(401);
    }

    /**
     * 2. Admin moderating an inappropriate product sets is_moderated to true and hides it.
     */
    public function test_admin_moderating_inappropriate_product_sets_is_moderated_and_hides_it(): void
    {
        $product = Product::create([
            'farmer_profile_id' => $this->farmerProfile->id,
            'category_id'       => $this->category->id,
            'name'              => 'Questionable Produce Listing',
            'slug'              => 'questionable-produce-' . uniqid(),
            'price'             => 10.00,
            'unit'              => 'piece',
            'stock_quantity'    => 5,
            'weekly_quota'      => 5,
            'is_available'      => true,
            'is_moderated'      => false,
        ]);

        $this->assertTrue(Product::available()->where('id', $product->id)->exists());

        // Admin flags the product via moderation endpoint
        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/admin/products/{$product->id}/moderate", [
                'action' => 'flag',
                'reason' => 'Misleading product description and non-organic claim.',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $product->refresh();
        $this->assertTrue((bool)$product->is_moderated);
        $this->assertFalse((bool)$product->is_available);

        // Product is excluded from available query scope
        $this->assertFalse(Product::available()->where('id', $product->id)->exists());
    }

    /**
     * 3. Admin moderating a toxic review hides or deletes it without breaking relational references in orders.
     */
    public function test_admin_moderating_toxic_review_hides_or_deletes_without_breaking_order(): void
    {
        $customer = User::create([
            'name'     => 'Aggressive Reviewer',
            'email'    => 'reviewer@client.com',
            'password' => bcrypt('Pass123!'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        $order = Order::create([
            'customer_id'       => $customer->id,
            'farmer_profile_id' => $this->farmerProfile->id,
            'total_amount'      => 15.00,
            'status'            => 'completed',
            'pickup_slot'       => '09:00 AM - 10:00 AM',
        ]);

        $review = Review::create([
            'order_id'          => $order->id,
            'customer_id'       => $customer->id,
            'farmer_profile_id' => $this->farmerProfile->id,
            'rating'            => 1,
            'comment'           => 'Toxic abusive comment violating community standards.',
            'is_moderated'      => false,
        ]);

        // 1. Admin hides the review
        $hideResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson("/api/v1/admin/moderation/reviews/{$review->id}", [
                'action' => 'hide',
            ]);

        $hideResponse->assertStatus(200);
        $this->assertTrue((bool)$review->fresh()->is_moderated);
        $this->assertDatabaseHas('orders', ['id' => $order->id]);

        // 2. Admin permanently deletes the toxic review
        $deleteResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->deleteJson("/api/v1/admin/reviews/{$review->id}/moderate");

        $deleteResponse->assertStatus(200);

        // Review is gone from DB
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);

        // Critically: Order remains completely intact in the database
        $this->assertDatabaseHas('orders', [
            'id'           => $order->id,
            'status'       => 'completed',
            'total_amount' => 15.00,
        ]);
    }
}
