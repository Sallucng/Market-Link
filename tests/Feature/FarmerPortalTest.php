<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmerPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $farmerUser;
    protected Farmer $farmer;
    protected Market $market;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->market = Market::create([
            'name' => 'Downtown Plaza',
            'address' => '100 Main St',
            'city' => 'Metropolis',
            'operating_days' => 'Saturday',
            'timings' => '08:00 AM - 01:00 PM',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $this->farmerUser = User::create([
            'name' => 'Green Farmer',
            'username' => 'greenfarmer',
            'email' => 'farmer@test.local',
            'contact_number' => '555-1234',
            'address' => 'Farm Valley',
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);

        $this->farmer = Farmer::create([
            'user_id' => $this->farmerUser->id,
            'market_id' => $this->market->id,
            'stall_name' => 'Green Valley Stall',
            'contact_person' => 'Green Farmer',
            'contact_number' => '555-1234',
            'address' => 'Stall 1',
            'operating_days' => 'Saturday',
            'pickup_time_windows' => '08:00 AM - 10:00 AM',
            'cutoff_hours' => 2,
        ]);

        $this->category = Category::create([
            'name' => 'Organic Vegetables',
            'description' => 'Fresh veggies',
        ]);
    }

    public function test_farmer_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->farmerUser)->get(route('farmer.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Green Valley Stall');
    }

    public function test_farmer_can_create_and_replenish_product(): void
    {
        $response = $this->actingAs($this->farmerUser)->post(route('farmer.products.store'), [
            'category_id' => $this->category->id,
            'name' => 'Organic Carrots',
            'description' => 'Sweet bunch',
            'price' => 3.50,
            'unit' => 'bunch',
            'stock_quantity' => 15,
            'weekly_recurring_stock' => 20,
        ]);

        $response->assertRedirect(route('farmer.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Organic Carrots',
            'farmer_id' => $this->farmer->id,
            'stock_quantity' => 15,
        ]);

        // Test replenish template
        $product = Product::where('name', 'Organic Carrots')->first();
        $product->stock_quantity = 0;
        $product->is_sold_out = true;
        $product->save();

        $replenishResponse = $this->actingAs($this->farmerUser)->post(route('farmer.products.replenish'));
        $replenishResponse->assertRedirect();

        $product->refresh();
        $this->assertEquals(20, $product->stock_quantity);
        $this->assertFalse((bool)$product->is_sold_out);
    }

    public function test_farmer_can_download_order_receipt(): void
    {
        $customer = User::create([
            'name' => 'John Buyer',
            'username' => 'johnbuyer',
            'email' => 'john@buyer.local',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        $product = Product::create([
            'farmer_id' => $this->farmer->id,
            'category_id' => $this->category->id,
            'name' => 'Farm Fresh Spinach',
            'price' => 4.00,
            'stock_quantity' => 10,
            'unit' => 'bunch',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'farmer_id' => $this->farmer->id,
            'market_id' => $this->market->id,
            'order_number' => 'ML-FARM-001',
            'order_status' => 'ready_for_pickup',
            'payment_status' => 'pending',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time_slot' => '08:00 AM - 10:00 AM',
            'total_amount' => 8.00,
            'payment_method' => 'pay_at_pickup',
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 4.00,
            'subtotal' => 8.00,
        ]);

        $response = $this->actingAs($this->farmerUser)->get(route('farmer.orders.receipt', $order->id));
        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));

        // Test marking completed updates payment_status to 'paid'
        $updateResponse = $this->actingAs($this->farmerUser)->post(route('farmer.orders.status', $order->id), [
            'status' => 'completed',
        ]);
        $updateResponse->assertRedirect();
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertTrue($order->fresh()->isPaid());
    }

    public function test_farmer_can_update_profile_and_geolocation(): void
    {
        $response = $this->actingAs($this->farmerUser)->post(route('farmer.profile.update'), [
            'stall_name' => 'Green Valley Organic Stall',
            'contact_person' => 'Green Grower',
            'contact_number' => '555-9876',
            'market_id' => $this->market->id,
            'address' => 'Stall 4A, Green Section',
            'operating_days' => 'Saturday, Sunday',
            'pickup_time_windows' => '08:00 AM - 10:00 AM, 10:30 AM - 12:30 PM',
            'cutoff_hours' => 3,
            'latitude' => 40.7135,
            'longitude' => -74.0070,
            'bio' => 'Family-owned organic farm practicing regenerative agriculture.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->farmer->refresh();
        $this->assertEquals('Green Valley Organic Stall', $this->farmer->stall_name);
        $this->assertEquals('Saturday, Sunday', $this->farmer->operating_days);
        $this->assertEquals(3, $this->farmer->cutoff_hours);
        $this->assertEquals(40.7135, $this->farmer->latitude);
        $this->assertEquals(-74.0070, $this->farmer->longitude);
    }

    public function test_farmer_can_manage_order_lifecycle_and_decline(): void
    {
        $customer = User::create([
            'name' => 'Alice Buyer',
            'username' => 'alicebuyer',
            'email' => 'alice@test.local',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        $prod = Product::create([
            'farmer_id' => $this->farmer->id,
            'category_id' => $this->category->id,
            'name' => 'Heirloom Beets',
            'price' => 5.00,
            'stock_quantity' => 5,
            'unit' => 'bunch',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'farmer_id' => $this->farmer->id,
            'market_id' => $this->market->id,
            'order_number' => 'ML-DECLINE-01',
            'order_status' => 'placed',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time_slot' => '08:00 AM - 10:00 AM',
            'total_amount' => 10.00,
            'payment_method' => 'pay_at_pickup',
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $prod->id,
            'quantity' => 2,
            'unit_price' => 5.00,
            'subtotal' => 10.00,
        ]);

        // Accept order
        $acceptRes = $this->actingAs($this->farmerUser)->post(route('farmer.orders.status', $order->id), [
            'status' => 'accepted',
        ]);
        $acceptRes->assertRedirect();
        $this->assertEquals('accepted', $order->fresh()->order_status);

        // Mark ready for pickup
        $readyRes = $this->actingAs($this->farmerUser)->post(route('farmer.orders.status', $order->id), [
            'status' => 'ready_for_pickup',
        ]);
        $readyRes->assertRedirect();
        $this->assertEquals('ready_for_pickup', $order->fresh()->order_status);

        // Decline order -> restores inventory
        $declineRes = $this->actingAs($this->farmerUser)->post(route('farmer.orders.status', $order->id), [
            'status' => 'declined',
            'reason' => 'Quality check failed for this harvest batch',
        ]);
        $declineRes->assertRedirect();
        $this->assertEquals('declined', $order->fresh()->order_status);
        $this->assertEquals(7, $prod->fresh()->stock_quantity); // 5 + 2 restored
    }

    public function test_farmer_can_respond_to_review(): void
    {
        $customer = User::create([
            'name' => 'Reviewer User',
            'username' => 'reviewer',
            'email' => 'rev@test.local',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'farmer_id' => $this->farmer->id,
            'market_id' => $this->market->id,
            'order_number' => 'ML-REV-01',
            'order_status' => 'completed',
            'payment_status' => 'paid',
            'pickup_date' => now()->toDateString(),
            'pickup_time_slot' => '08:00 AM - 10:00 AM',
            'total_amount' => 10.00,
            'payment_method' => 'pay_at_pickup',
        ]);

        $review = \App\Models\Review::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'farmer_id' => $this->farmer->id,
            'rating' => 5,
            'comment' => 'The best products in the market!',
        ]);

        $respondRes = $this->actingAs($this->farmerUser)->post(route('farmer.reviews.respond', $review->id), [
            'farmer_response' => 'Thank you for supporting our family farm!',
        ]);

        $respondRes->assertRedirect();
        $respondRes->assertSessionHas('success');
        $this->assertEquals('Thank you for supporting our family farm!', $review->fresh()->farmer_response);
    }
}
