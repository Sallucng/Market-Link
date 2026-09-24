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
}
