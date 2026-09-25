<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Farmer $farmer;
    private Market $market;
    private Category $category;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->market = Market::create([
            'name' => 'Union Square Greenmarket',
            'slug' => 'union-square-greenmarket',
            'address' => 'Union Square W & E 17th St',
            'city' => 'New York',
            'operating_days' => 'Mon, Wed, Fri, Sat',
            'timings' => '8:00 AM - 6:00 PM',
            'latitude' => 40.7359,
            'longitude' => -73.9911,
            'is_active' => true,
        ]);

        $farmerUser = User::create([
            'name' => 'Hudson Valley Orchards',
            'username' => 'hudsonorchards',
            'email' => 'hudson@example.com',
            'password' => bcrypt('password'),
            'role' => 'farmer',
            'is_approved' => true,
        ]);

        $this->farmer = Farmer::create([
            'user_id' => $farmerUser->id,
            'market_id' => $this->market->id,
            'stall_name' => 'Hudson Valley Orchards Stall',
            'contact_person' => 'Hudson Farmer',
            'contact_number' => '555-1234',
            'cutoff_hours' => 2,
            'pickup_time_windows' => '08:00 AM - 10:00 AM, 10:00 AM - 12:00 PM',
        ]);

        $this->category = Category::create([
            'name' => 'Fresh Fruits',
            'description' => 'Orchard harvested fruits',
        ]);

        $this->product = Product::create([
            'farmer_id' => $this->farmer->id,
            'category_id' => $this->category->id,
            'name' => 'Honeycrisp Apples',
            'slug' => 'honeycrisp-apples',
            'price' => 3.50,
            'stock_quantity' => 15,
            'unit' => 'lb',
            'is_sold_out' => false,
        ]);

        $this->customer = User::create([
            'name' => 'Alice Customer',
            'username' => 'alicec',
            'email' => 'alice@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);
    }

    public function test_customer_can_access_dashboard()
    {
        $response = $this->actingAs($this->customer)->get('/customer/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Customer Dashboard');
        $response->assertSee('Active Pre-Orders');
    }

    public function test_customer_can_toggle_favorite_market_and_product()
    {
        // Toggle market favorite
        $response = $this->actingAs($this->customer)->post('/customer/favorites/toggle', [
            'item_type' => 'market',
            'item_id' => $this->market->id,
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('favorites', [
            'customer_id' => $this->customer->id,
            'item_type' => 'market',
            'item_id' => $this->market->id,
        ]);

        // Toggle product favorite
        $this->actingAs($this->customer)->post('/customer/favorites/toggle', [
            'item_type' => 'product',
            'item_id' => $this->product->id,
        ]);

        $favResponse = $this->actingAs($this->customer)->get('/customer/favorites');
        $favResponse->assertStatus(200);
        $favResponse->assertSee('Union Square Greenmarket');
        $favResponse->assertSee('Honeycrisp Apples');
    }

    public function test_customer_can_modify_preorder_before_cutoff()
    {
        $order = Order::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'market_id' => $this->market->id,
            'order_number' => 'ML-TEST1234',
            'order_status' => 'placed',
            'pickup_date' => Carbon::tomorrow()->toDateString(),
            'pickup_time_slot' => '08:00 AM - 10:00 AM',
            'total_amount' => 10.50,
            'cutoff_time' => Carbon::tomorrow()->setTime(6, 0),
        ]);

        $response = $this->actingAs($this->customer)->post("/customer/orders/{$order->id}/modify", [
            'pickup_date' => Carbon::tomorrow()->toDateString(),
            'pickup_time_slot' => '10:00 AM - 12:00 PM',
            'notes' => 'Please pack in paper bag',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'pickup_time_slot' => '10:00 AM - 12:00 PM',
            'notes' => 'Please pack in paper bag',
        ]);
    }

    public function test_customer_can_review_completed_order_and_specific_product()
    {
        $order = Order::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'market_id' => $this->market->id,
            'order_number' => 'ML-REV12345',
            'order_status' => 'completed',
            'pickup_date' => Carbon::yesterday()->toDateString(),
            'pickup_time_slot' => '08:00 AM - 10:00 AM',
            'total_amount' => 7.00,
        ]);

        $response = $this->actingAs($this->customer)->post("/customer/orders/{$order->id}/review", [
            'rating' => 5,
            'comment' => 'The apples were crisp and delicious! Smooth stall pickup.',
            'product_id' => $this->product->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'rating' => 5,
        ]);
    }

    public function test_contact_page_has_google_maps_embed()
    {
        $response = $this->get('/contact');
        $response->assertStatus(200);
        $response->assertSee('maps.google.com/maps');
    }

    public function test_checkout_rejects_order_if_stock_insufficient()
    {
        // Put item in session cart requesting 20 when stock is 15
        $cart = [
            $this->product->id => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'price' => $this->product->price,
                'quantity' => 20,
                'farmer_id' => $this->farmer->id,
                'farmer_name' => $this->farmer->stall_name,
                'market_name' => $this->market->name,
                'pickup_time_windows' => '08:00 AM - 10:00 AM',
                'operating_days' => 'Saturday',
                'cutoff_hours' => 2,
            ]
        ];

        $response = $this->actingAs($this->customer)
            ->withSession(['cart' => $cart])
            ->post('/checkout/place', [
                'pickup_date' => [$this->farmer->id => Carbon::tomorrow()->toDateString()],
                'pickup_time_slot' => [$this->farmer->id => '08:00 AM - 10:00 AM'],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        // Product stock should still be 15
        $this->assertEquals(15, $this->product->fresh()->stock_quantity);
    }

    public function test_customer_can_download_pdf_receipt()
    {
        $order = Order::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'market_id' => $this->market->id,
            'order_number' => 'ML-TEST-001',
            'order_status' => 'completed',
            'payment_status' => 'paid',
            'pickup_date' => Carbon::tomorrow()->toDateString(),
            'pickup_time_slot' => '08:00 AM - 10:00 AM',
            'total_amount' => 14.00,
            'payment_method' => 'pay_at_pickup',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 4,
            'unit_price' => 3.50,
            'subtotal' => 14.00,
        ]);

        $response = $this->actingAs($this->customer)->get("/customer/orders/{$order->id}/receipt");

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('MarketLink-Receipt-ML-TEST-001.pdf', $response->headers->get('content-disposition'));
    }

    public function test_customer_cannot_download_other_customer_receipt()
    {
        $otherCustomer = User::create([
            'name' => 'Other Customer',
            'username' => 'othercust',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $order = Order::create([
            'customer_id' => $otherCustomer->id,
            'farmer_id' => $this->farmer->id,
            'market_id' => $this->market->id,
            'order_number' => 'ML-TEST-002',
            'order_status' => 'placed',
            'pickup_date' => Carbon::tomorrow()->toDateString(),
            'pickup_time_slot' => '08:00 AM - 10:00 AM',
            'total_amount' => 7.00,
            'payment_method' => 'pay_at_pickup',
        ]);

        $response = $this->actingAs($this->customer)->get("/customer/orders/{$order->id}/receipt");
        $response->assertStatus(404);
    }

    public function test_customer_can_cancel_order_and_restore_stock()
    {
        $this->product->update(['stock_quantity' => 10]);

        $order = Order::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'market_id' => $this->market->id,
            'order_number' => 'ML-TEST-CANCEL',
            'order_status' => 'placed',
            'pickup_date' => Carbon::tomorrow()->toDateString(),
            'pickup_time_slot' => '08:00 AM - 10:00 AM',
            'total_amount' => 10.50,
            'payment_method' => 'pay_at_pickup',
            'cutoff_time' => Carbon::now()->addHours(6),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'unit_price' => 3.50,
            'subtotal' => 10.50,
        ]);

        $response = $this->actingAs($this->customer)->post("/customer/orders/{$order->id}/cancel");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('cancelled', $order->fresh()->order_status);
        $this->assertEquals(13, $this->product->fresh()->stock_quantity);
    }

    public function test_customer_can_modify_pickup_schedule()
    {
        $order = Order::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'market_id' => $this->market->id,
            'order_number' => 'ML-TEST-MODIFY',
            'order_status' => 'placed',
            'pickup_date' => Carbon::tomorrow()->toDateString(),
            'pickup_time_slot' => '08:00 AM - 10:00 AM',
            'total_amount' => 7.00,
            'payment_method' => 'pay_at_pickup',
            'cutoff_time' => Carbon::now()->addHours(6),
        ]);

        $newDate = Carbon::tomorrow()->addDay()->toDateString();
        $response = $this->actingAs($this->customer)->post("/customer/orders/{$order->id}/modify", [
            'pickup_date' => $newDate,
            'pickup_time_slot' => '10:00 AM - 12:00 PM',
            'notes' => 'Please pack in brown paper',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fresh = $order->fresh();
        $this->assertEquals($newDate, $fresh->pickup_date->format('Y-m-d'));
        $this->assertEquals('10:00 AM - 12:00 PM', $fresh->pickup_time_slot);
        $this->assertEquals('Please pack in brown paper', $fresh->notes);
    }

    public function test_customer_can_toggle_favorites_for_market_farmer_and_product()
    {
        // Toggle market
        $resMarket = $this->actingAs($this->customer)->post(route('customer.favorites.toggle'), [
            'item_type' => 'market',
            'item_id' => $this->market->id,
        ]);
        $resMarket->assertRedirect();
        $this->assertDatabaseHas('favorites', [
            'customer_id' => $this->customer->id,
            'item_type' => 'market',
            'item_id' => $this->market->id,
        ]);

        // Toggle product
        $resProd = $this->actingAs($this->customer)->post(route('customer.favorites.toggle'), [
            'item_type' => 'product',
            'item_id' => $this->product->id,
        ]);
        $resProd->assertRedirect();
        $this->assertDatabaseHas('favorites', [
            'customer_id' => $this->customer->id,
            'item_type' => 'product',
            'item_id' => $this->product->id,
        ]);

        // Toggle farmer
        $resFarmer = $this->actingAs($this->customer)->post(route('customer.favorites.toggle'), [
            'item_type' => 'farmer',
            'item_id' => $this->farmer->id,
        ]);
        $resFarmer->assertRedirect();
        $this->assertDatabaseHas('favorites', [
            'customer_id' => $this->customer->id,
            'item_type' => 'farmer',
            'item_id' => $this->farmer->id,
        ]);
    }
}
