<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmerOrderTest extends TestCase
{
    use RefreshDatabase;

    private User $farmerUser;
    private string $farmerToken;
    private User $customerUser;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->farmerUser = User::create([
            'name' => 'Farmer Mark',
            'email' => 'mark@farm.com',
            'password' => bcrypt('Password123!'),
            'role' => 'farmer',
            'status' => 'active',
        ]);

        $profile = $this->farmerUser->farmerProfile()->create([
            'farm_name' => 'Mark Organic Farms',
            'address' => '200 Oak Way',
            'city' => 'Fresno',
            'state' => 'CA',
            'postal_code' => '93720',
            'approval_status' => 'approved',
        ]);

        $this->farmerToken = $this->farmerUser->createToken('farmer-token')->plainTextToken;

        $category = Category::create([
            'name' => 'Root Vegetables',
            'slug' => 'root-vegetables',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'farmer_profile_id' => $profile->id,
            'category_id' => $category->id,
            'name' => 'Fresh Beets',
            'slug' => 'fresh-beets',
            'price' => 3.00,
            'unit' => 'bunch',
            'stock_quantity' => 20,
            'weekly_stock' => 20,
            'is_available' => true,
        ]);

        $this->customerUser = User::create([
            'name' => 'Customer Dave',
            'email' => 'dave@client.com',
            'password' => bcrypt('Password123!'),
            'role' => 'customer',
            'status' => 'active',
        ]);
    }

    public function test_farmer_order_lifecycle_accept_ready_complete(): void
    {
        $profile = $this->farmerUser->farmerProfile;

        $order = Order::create([
            'customer_id' => $this->customerUser->id,
            'farmer_profile_id' => $profile->id,
            'subtotal' => 6.00,
            'total_amount' => 6.00,
            'status' => 'pending',
            'pickup_slot' => '10:00 AM - 11:00 AM',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'unit_price' => $this->product->price,
            'quantity' => 2,
            'subtotal' => 6.00,
        ]);

        // 1. List incoming orders
        $listResponse = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->getJson('/api/farmer/orders');

        $listResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');

        // 2. View order details
        $detailResponse = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->getJson("/api/farmer/orders/{$order->id}");

        $detailResponse->assertStatus(200)
            ->assertJsonPath('data.items.0.product_name', 'Fresh Beets');

        // 3. Accept order
        $acceptResponse = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->postJson("/api/farmer/orders/{$order->id}/accept");

        $acceptResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'accepted');

        // 4. Mark ready for pickup
        $readyResponse = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->postJson("/api/farmer/orders/{$order->id}/ready-for-pickup");

        $readyResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'ready_for_pickup');

        // 5. Complete order
        $completeResponse = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->postJson("/api/farmer/orders/{$order->id}/complete");

        $completeResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'completed');

        // 6. Check statistics
        $statsResponse = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->getJson('/api/farmer/statistics');

        $statsResponse->assertStatus(200)
            ->assertJsonPath('data.completed_orders', 1)
            ->assertJsonPath('data.revenue_summary.completed_revenue', 6)
            ->assertJsonPath('data.best_selling_products.0.product_name', 'Fresh Beets');
    }

    public function test_farmer_can_decline_order_and_inventory_is_restocked(): void
    {
        $profile = $this->farmerUser->farmerProfile;

        // Customer ordered 5 bunches, current stock is 15
        $this->product->update(['stock_quantity' => 15]);

        $order = Order::create([
            'customer_id' => $this->customerUser->id,
            'farmer_profile_id' => $profile->id,
            'total_amount' => 15.00,
            'status' => 'pending',
            'pickup_slot' => '11:00 AM - 12:00 PM',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'unit_price' => $this->product->price,
            'quantity' => 5,
            'subtotal' => 15.00,
        ]);

        $declineResponse = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->postJson("/api/farmer/orders/{$order->id}/decline", [
                'decline_reason' => 'Frost damage overnight ruined remaining stock.',
            ]);

        $declineResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'declined')
            ->assertJsonPath('data.decline_reason', 'Frost damage overnight ruined remaining stock.');

        // Stock restored from 15 to 20
        $this->product->refresh();
        $this->assertEquals(20, $this->product->stock_quantity);
    }

    public function test_farmer_can_update_order_status_via_unified_endpoint(): void
    {
        $profile = $this->farmerUser->farmerProfile;

        $order = Order::create([
            'customer_id' => $this->customerUser->id,
            'farmer_profile_id' => $profile->id,
            'total_amount' => 12.00,
            'status' => 'pending',
            'pickup_slot' => '10:00 AM - 11:00 AM',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'unit_price' => $this->product->price,
            'quantity' => 4,
            'subtotal' => 12.00,
        ]);

        // Accept via unified status endpoint
        $response = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->putJson("/api/farmer/orders/{$order->id}/status", [
                'status' => 'accepted',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'accepted');

        // Ready for pickup via unified status endpoint
        $response = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->putJson("/api/farmer/orders/{$order->id}/status", [
                'status' => 'ready_for_pickup',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'ready_for_pickup');
    }

    public function test_cannot_update_order_status_if_cutoff_time_passed(): void
    {
        $profile = $this->farmerUser->farmerProfile;

        $order = Order::create([
            'customer_id' => $this->customerUser->id,
            'farmer_profile_id' => $profile->id,
            'total_amount' => 9.00,
            'status' => 'pending',
            'pickup_slot' => '09:00 AM - 10:00 AM',
            'cutoff_time' => now()->subMinutes(30),
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->putJson("/api/farmer/orders/{$order->id}/status", [
                'status' => 'accepted',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error');
    }

    public function test_farmer_dashboard_statistics_endpoint(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->getJson('/api/farmer/dashboard/statistics');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'data' => [
                    'total_orders',
                    'pending_orders',
                    'accepted_orders',
                    'ready_orders',
                    'completed_orders',
                    'declined_orders',
                    'cancelled_orders',
                    'revenue_summary' => [
                        'completed_revenue',
                        'currency',
                    ],
                    'best_selling_products',
                ],
            ]);
    }
}
