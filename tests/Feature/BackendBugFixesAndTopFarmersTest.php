<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Conversation;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackendBugFixesAndTopFarmersTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $farmerUser;
    private Farmer $farmer;
    private Market $market;
    private Category $category;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->market = Market::create([
            'name' => 'Metro Plaza Market',
            'city' => 'Metroville',
            'state' => 'CA',
            'address' => '123 Market St',
            'postal_code' => '90001',
            'operating_days' => 'Saturday, Sunday',
        ]);

        $this->farmerUser = User::create([
            'name' => 'John Grower',
            'email' => 'john@grower.com',
            'password' => bcrypt('password123'),
            'role' => 'farmer',
            'status' => 'active',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $this->farmer = Farmer::create([
            'user_id' => $this->farmerUser->id,
            'market_id' => $this->market->id,
            'stall_name' => 'Grower John Farm',
            'contact_person' => 'John Grower',
            'contact_number' => '555-111-2222',
            'address' => 'Stall #5',
            'operating_days' => 'Saturday',
            'pickup_time_windows' => '08:00 AM - 12:00 PM',
            'cutoff_hours' => 2,
            'is_approved' => true,
            'approval_status' => 'approved',
        ]);

        $this->category = Category::create([
            'name' => 'Vegetables',
            'slug' => 'vegetables',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'farmer_id' => $this->farmer->id,
            'farmer_profile_id' => $this->farmer->id,
            'category_id' => $this->category->id,
            'name' => 'Fresh Heirloom Tomatoes',
            'price' => 4.50,
            'unit' => 'lb',
            'stock_quantity' => 20,
            'is_available' => true,
            'is_sold_out' => false,
        ]);

        $this->customer = User::create([
            'name' => 'Alice Customer',
            'email' => 'alice@customer.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'status' => 'active',
            'is_active' => true,
            'is_approved' => true,
        ]);
    }

    public function test_home_page_renders_top_rated_farmers_carousel(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewHas('topFarmers');
        $response->assertSee('Top 10 Rated Growers');
        $response->assertSee('id="topFarmersTrack"', false);
        $response->assertSee('id="topFarmersPrev"', false);
        $response->assertSee('id="topFarmersNext"', false);
        $response->assertSee('Grower John Farm');
    }

    public function test_farmer_order_status_cannot_be_updated_when_already_declined_cancelled_or_completed(): void
    {
        $order = Order::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'market_id' => $this->market->id,
            'order_number' => 'ORD-TEST-001',
            'order_status' => 'placed',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time_slot' => '09:00 AM - 10:00 AM',
            'total_amount' => 9.00,
            'payment_method' => 'Cash on Pickup',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 4.50,
            'subtotal' => 9.00,
        ]);

        // 1. Farmer declines with a reason
        $this->actingAs($this->farmerUser)
            ->post(route('farmer.orders.status', $order->id), [
                'status' => 'declined',
                'reason' => 'Frost damage ruined harvest.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('declined', $order->order_status);
        $this->assertEquals('declined', $order->payment_status);
        $this->assertEquals('Frost damage ruined harvest.', $order->decline_reason);
        // Stock restored from 20 to 22
        $this->assertEquals(22, $this->product->fresh()->stock_quantity);

        // 2. Farmer attempts to change status again (should be blocked to prevent double-restoring stock)
        $this->actingAs($this->farmerUser)
            ->post(route('farmer.orders.status', $order->id), [
                'status' => 'declined',
                'reason' => 'Duplicate attempt',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        // Stock must still be 22, not 24!
        $this->assertEquals(22, $this->product->fresh()->stock_quantity);
    }

    public function test_customer_cancelling_or_modifying_order_notifies_farmer(): void
    {
        $order = Order::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'market_id' => $this->market->id,
            'order_number' => 'ORD-NOTIF-01',
            'order_status' => 'placed',
            'pickup_date' => now()->addDays(2)->toDateString(),
            'pickup_time_slot' => '09:00 AM - 10:00 AM',
            'total_amount' => 4.50,
            'cutoff_time' => now()->addDay(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 4.50,
            'subtotal' => 4.50,
        ]);

        // Customer modifies pickup
        $newPickupDate = now()->addDays(3)->toDateString();
        $this->actingAs($this->customer)
            ->post(route('customer.orders.modify', $order->id), [
                'pickup_date' => $newPickupDate,
                'pickup_time_slot' => '11:00 AM - 12:00 PM',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->farmerUser->id,
            'title' => 'Pre-Order #ORD-NOTIF-01 Rescheduled',
        ]);

        // Customer cancels order
        $this->actingAs($this->customer)
            ->post(route('customer.orders.cancel', $order->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->farmerUser->id,
            'title' => 'Pre-Order #ORD-NOTIF-01 Cancelled',
        ]);
    }

    public function test_chat_message_creates_in_app_notification_for_recipient(): void
    {
        $conversation = Conversation::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'subject' => 'Question about heirloom variety',
            'last_message_at' => now(),
        ]);

        // Customer sends message to farmer
        $this->actingAs($this->customer)
            ->postJson(route('customer.messages.send', $conversation->id), [
                'body' => 'Are your tomatoes organic and unsprayed?',
            ])
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->farmerUser->id,
            'type' => 'chat',
        ]);
    }

    public function test_farmer_cannot_delete_product_with_active_pre_orders(): void
    {
        $order = Order::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'market_id' => $this->market->id,
            'order_number' => 'ORD-DEL-PROD',
            'order_status' => 'accepted',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time_slot' => '09:00 AM',
            'total_amount' => 4.50,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 4.50,
            'subtotal' => 4.50,
        ]);

        // Farmer attempts to delete product
        $this->actingAs($this->farmerUser)
            ->delete(route('farmer.products.destroy', $this->product->id))
            ->assertRedirect()
            ->assertSessionHas('error');

        // Product still exists!
        $this->assertDatabaseHas('products', ['id' => $this->product->id]);
    }

    public function test_review_validation_rejects_products_not_in_order_and_sets_farmer_response_timestamp(): void
    {
        $order = Order::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'market_id' => $this->market->id,
            'order_number' => 'ORD-REV-01',
            'order_status' => 'completed',
            'pickup_date' => now()->subDay()->toDateString(),
            'pickup_time_slot' => '10:00 AM',
            'total_amount' => 4.50,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 4.50,
            'subtotal' => 4.50,
        ]);

        // Another product from a completely different farmer/stall
        $otherFarmerUser = User::create([
            'name' => 'Other Farmer',
            'email' => 'other@farmer.com',
            'password' => bcrypt('password123'),
            'role' => 'farmer',
            'status' => 'active',
            'is_active' => true,
            'is_approved' => true,
        ]);
        $otherFarmer = Farmer::create([
            'user_id' => $otherFarmerUser->id,
            'market_id' => $this->market->id,
            'stall_name' => 'Other Farm Stall',
            'contact_person' => 'Other Person',
            'contact_number' => '555-999-8888',
            'address' => 'Stall #99',
            'operating_days' => 'Sunday',
            'pickup_time_windows' => '09:00 AM - 12:00 PM',
            'cutoff_hours' => 2,
            'is_approved' => true,
        ]);
        $otherProduct = Product::create([
            'farmer_id' => $otherFarmer->id,
            'farmer_profile_id' => $otherFarmer->id,
            'category_id' => $this->category->id,
            'name' => 'Unordered Other Farm Carrots',
            'price' => 3.00,
            'unit' => 'bunch',
            'stock_quantity' => 10,
            'is_available' => true,
        ]);

        // Customer attempts to review the unordered carrots for this order
        $this->actingAs($this->customer)
            ->post(route('customer.orders.review', $order->id), [
                'rating' => 5,
                'comment' => 'Great product quality and fresh harvest!',
                'product_id' => $otherProduct->id,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        // Now review valid product in this order
        $this->actingAs($this->customer)
            ->post(route('customer.orders.review', $order->id), [
                'rating' => 5,
                'comment' => 'The tomatoes were wonderfully sweet and juicy.',
                'product_id' => $this->product->id,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $review = Review::where('order_id', $order->id)->firstOrFail();
        $this->assertNull($review->responded_at);

        // Farmer responds to review
        $this->actingAs($this->farmerUser)
            ->post(route('farmer.reviews.respond', $review->id), [
                'farmer_response' => 'Thank you so much Alice! See you this Saturday at Downtown Plaza.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $review->refresh();
        $this->assertNotNull($review->responded_at);
        $this->assertEquals('Thank you so much Alice! See you this Saturday at Downtown Plaza.', $review->farmer_response);

        // Customer notified of farmer reply
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->customer->id,
            'type' => 'review',
        ]);
    }

    public function test_favorite_toggle_rejects_non_existent_item_id(): void
    {
        $this->actingAs($this->customer)
            ->postJson(route('customer.favorites.toggle'), [
                'item_type' => 'product',
                'item_id' => 9999999, // non-existent
            ])
            ->assertStatus(404)
            ->assertJson(['error' => 'Item not found.']);

        $this->assertDatabaseMissing('favorites', [
            'customer_id' => $this->customer->id,
            'item_id' => 9999999,
        ]);
    }
}
