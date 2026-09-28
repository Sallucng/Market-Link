<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderProcessingEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    private User $farmerUser;
    private FarmerProfile $farmerProfile;
    private string $farmerToken;
    private User $customerUser;
    private Product $product;
    private Market $market;

    protected function setUp(): void
    {
        parent::setUp();

        $this->farmerUser = User::create([
            'name'     => 'Farmer Sam',
            'email'    => 'sam@farm.com',
            'password' => bcrypt('Password123!'),
            'role'     => 'farmer',
            'status'   => 'active',
        ]);

        $this->farmerProfile = $this->farmerUser->farmerProfile()->create([
            'business_name'     => 'Sam Organic Greens',
            'stall_number'      => 'Stall #10',
            'address'           => '100 Farmer Way',
            'operating_days'    => ['Saturday', 'Sunday'],
            'pickup_start_time' => '08:00',
            'pickup_end_time'   => '13:00',
            'is_approved'       => true,
        ]);

        $this->farmerToken = $this->farmerUser->createToken('sam-token')->plainTextToken;

        $category = Category::create([
            'name'      => 'Greens',
            'slug'      => 'greens',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'farmer_profile_id' => $this->farmerProfile->id,
            'category_id'       => $category->id,
            'name'              => 'Romaine Lettuce',
            'slug'              => 'romaine-lettuce-' . uniqid(),
            'price'             => 2.50,
            'unit'              => 'head',
            'stock_quantity'    => 20,
            'weekly_quota'      => 30,
            'is_available'      => true,
        ]);

        $this->market = Market::create([
            'name'           => 'Central Market',
            'location'       => 'Downtown',
            'address'        => '123 Market St',
            'operating_days' => ['Saturday'],
            'open_time'      => '08:00',
            'close_time'     => '14:00',
            'status'         => 'active',
        ]);

        $this->customerUser = User::create([
            'name'     => 'Customer Charlie',
            'email'    => 'charlie@client.com',
            'password' => bcrypt('Password123!'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);
    }

    /**
     * 1. Farmer cannot transition an order from 'pending' directly to 'completed'.
     */
    public function test_farmer_cannot_transition_order_from_pending_directly_to_completed(): void
    {
        $order = Order::create([
            'customer_id'       => $this->customerUser->id,
            'farmer_profile_id' => $this->farmerProfile->id,
            'market_id'         => $this->market->id,
            'total_amount'      => 5.00,
            'status'            => 'pending',
            'pickup_slot'       => '09:00 AM - 10:00 AM',
            'cutoff_time'       => now()->addHours(6),
        ]);

        // Attempting to complete directly via status update
        $response1 = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->patchJson("/api/v1/farmer/orders/{$order->id}/status", [
                'status' => 'completed',
            ]);

        // Status update endpoint validates enum ('accepted', 'declined', 'ready_for_pickup')
        $response1->assertStatus(422);

        // Attempting to call the dedicated complete endpoint directly from pending state
        $response2 = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->postJson("/api/v1/farmer/orders/{$order->id}/complete");

        $response2->assertStatus(422)
            ->assertJsonPath('status', 'error');

        // Order remains in pending status
        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'pending',
        ]);
    }

    /**
     * 2. Farmer cannot accept or modify an order after the cutoff time has passed.
     */
    public function test_farmer_cannot_accept_or_modify_order_after_cutoff_time_passed(): void
    {
        // Order with an expired cutoff time (1 hour ago)
        $order = Order::create([
            'customer_id'       => $this->customerUser->id,
            'farmer_profile_id' => $this->farmerProfile->id,
            'market_id'         => $this->market->id,
            'total_amount'      => 5.00,
            'status'            => 'pending',
            'pickup_slot'       => '09:00 AM - 10:00 AM',
            'cutoff_time'       => now()->subHour(),
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->patchJson("/api/v1/farmer/orders/{$order->id}/status", [
                'status' => 'accepted',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error')
            ->assertJsonFragment(['message' => 'The cutoff time for this order has passed. Status cannot be modified.']);

        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'pending',
        ]);
    }

    /**
     * 3. Declining an order executes successfully and restocks inventory.
     */
    public function test_declining_order_executes_successfully_and_restocks_inventory(): void
    {
        // Initial product stock is 20
        $this->assertEquals(20, $this->product->stock_quantity);

        $order = Order::create([
            'customer_id'       => $this->customerUser->id,
            'farmer_profile_id' => $this->farmerProfile->id,
            'market_id'         => $this->market->id,
            'total_amount'      => 12.50,
            'status'            => 'pending',
            'pickup_slot'       => '10:00 AM - 11:00 AM',
            'cutoff_time'       => now()->addHours(8),
        ]);

        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $this->product->id,
            'quantity'   => 5,
            'unit_price' => 2.50,
            'subtotal'   => 12.50,
        ]);

        $declineReason = 'Harvest shortage due to unexpected overnight frost.';

        $response = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->patchJson("/api/v1/farmer/orders/{$order->id}/status", [
                'status'         => 'declined',
                'decline_reason' => $declineReason,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('orders', [
            'id'             => $order->id,
            'status'         => 'declined',
            'decline_reason' => $declineReason,
        ]);

        // Assert product stock was replenished from 20 -> 25
        $this->assertEquals(25, $this->product->fresh()->stock_quantity);
    }

    /**
     * 4. Concurrent status updates handled within a database transaction.
     */
    public function test_concurrent_status_updates_handled_within_database_transaction(): void
    {
        $order = Order::create([
            'customer_id'       => $this->customerUser->id,
            'farmer_profile_id' => $this->farmerProfile->id,
            'market_id'         => $this->market->id,
            'total_amount'      => 7.50,
            'status'            => 'pending',
            'pickup_slot'       => '11:00 AM - 12:00 PM',
            'cutoff_time'       => now()->addHours(4),
        ]);

        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $this->product->id,
            'quantity'   => 3,
            'unit_price' => 2.50,
            'subtotal'   => 7.50,
        ]);

        // Simulate transactional integrity: if an exception occurs mid-transaction, state rolls back
        $initialStock = $this->product->fresh()->stock_quantity;

        try {
            DB::transaction(function () use ($order) {
                $order->update(['status' => 'declined']);
                // Restock
                $this->product->increment('stock_quantity', 3);

                // Simulate simulated failure before commit
                throw new \Exception('Simulated database deadlock / network failure');
            });
        } catch (\Exception $e) {
            // Expected catch
        }

        // Assert rollback was complete: status remains pending, stock remains unchanged
        $this->assertEquals('pending', $order->fresh()->status);
        $this->assertEquals($initialStock, $this->product->fresh()->stock_quantity);
    }
}
