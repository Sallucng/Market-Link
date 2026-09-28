<?php

namespace Tests\Feature;

use App\Mail\FarmerApprovedMail;
use App\Mail\NewOrderFarmerAlertMail;
use App\Mail\OrderPlacedCustomerMail;
use App\Mail\OrderStatusUpdateCustomerMail;
use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FaqAndEmailNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_faq_page_loads_successfully_with_categories(): void
    {
        $response = $this->get(route('faq'));

        $response->assertStatus(200);
        $response->assertSee('Frequently Asked Questions');
        $response->assertSee('Pre-Orders &amp; Pickup', false);
        $response->assertSee('Farmers &amp; Stalls', false);
        $response->assertSee('Safety &amp; Moderation', false);
        $response->assertSee('Emails &amp; Notifications', false);
    }

    public function test_faq_page_renders_with_rtl_in_urdu(): void
    {
        $response = $this->withSession(['locale' => 'ur'])->get(route('faq'));

        $response->assertStatus(200);
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('lang="ur"', false);
        $response->assertSee('عام سوالات (FAQs)');
    }

    public function test_placing_order_dispatches_customer_and_farmer_emails(): void
    {
        Mail::fake();

        $market = Market::create([
            'name' => 'Downtown Farmers Market',
            'slug' => 'downtown-farmers-market',
            'address' => '100 Main St',
            'city' => 'Metropolis',
            'operating_days' => 'Saturday, Sunday',
            'open_time' => '08:00 AM',
            'close_time' => '02:00 PM',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'is_active' => true,
        ]);

        $farmerUser = User::create([
            'name' => 'Green Valley Grower',
            'username' => 'greenvalley',
            'email' => 'grower@example.com',
            'password' => bcrypt('password'),
            'role' => 'farmer',
            'contact_number' => '1234567890',
            'address' => 'Farm Rd 1',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $farmer = Farmer::create([
            'user_id' => $farmerUser->id,
            'stall_name' => 'Green Valley Organics',
            'market_id' => $market->id,
            'contact_person' => 'John Grower',
            'contact_number' => '1234567890',
            'address' => 'Stall 4',
            'cutoff_hours' => 2,
            'is_approved' => true,
        ]);

        $category = Category::create([
            'name' => 'Vegetables',
            'slug' => 'vegetables',
            'is_active' => true,
        ]);

        $product = Product::create([
            'farmer_id' => $farmer->id,
            'category_id' => $category->id,
            'name' => 'Organic Carrots',
            'slug' => 'organic-carrots',
            'price' => 4.50,
            'unit' => 'bunch',
            'stock_quantity' => 25,
            'weekly_stock' => 25,
            'is_available' => true,
        ]);

        $customer = User::create([
            'name' => 'Sarah Shopper',
            'username' => 'sarah_shopper_unique',
            'email' => 'shopper@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'contact_number' => '9876543210',
            'address' => '456 Maple Ave',
            'is_active' => true,
        ]);

        $cart = [
            $product->id => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => 4.50,
                'quantity' => 2,
                'farmer_id' => $farmer->id,
                'farmer_name' => $farmer->stall_name,
                'market_name' => $market->name,
                'pickup_time_windows' => '9 AM - 1 PM',
                'operating_days' => 'Saturday',
                'cutoff_hours' => 2,
            ],
        ];

        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.place'), [
                'pickup_date' => [$farmer->id => now()->addDays(2)->format('Y-m-d')],
                'pickup_time_slot' => [$farmer->id => '10:00 AM - 11:00 AM'],
            ]);

        $response->assertRedirect(route('customer.orders.index'));

        Mail::assertSent(OrderPlacedCustomerMail::class, function ($mail) use ($customer) {
            return $mail->hasTo($customer->email);
        });

        Mail::assertSent(NewOrderFarmerAlertMail::class, function ($mail) use ($farmerUser) {
            return $mail->hasTo($farmerUser->email);
        });
    }

    public function test_farmer_updating_order_status_dispatches_customer_email(): void
    {
        Mail::fake();

        $farmerUser = User::create([
            'name' => 'Farmer Bob',
            'username' => 'farmer_bob',
            'email' => 'bob@farmer.com',
            'password' => bcrypt('password'),
            'role' => 'farmer',
            'contact_number' => '1234567890',
            'address' => 'Bob Rd',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $farmer = Farmer::create([
            'user_id' => $farmerUser->id,
            'stall_name' => 'Bob Farm',
            'contact_person' => 'Bob',
            'contact_number' => '1234567890',
            'address' => 'Stall 2',
            'cutoff_hours' => 2,
            'is_approved' => true,
        ]);

        $customer = User::create([
            'name' => 'Jane Buyer',
            'username' => 'jane_buyer',
            'email' => 'jane@buyer.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'contact_number' => '9876543210',
            'address' => '100 Street',
            'is_active' => true,
        ]);

        $order = Order::create([
            'farmer_id' => $farmer->id,
            'customer_id' => $customer->id,
            'order_number' => 'ML-TEST1234',
            'order_status' => 'accepted',
            'total_amount' => 15.00,
            'payment_method' => 'pay_at_pickup',
            'pickup_date' => now()->addDay(),
            'pickup_time_slot' => '10:00 AM - 11:00 AM',
        ]);

        $response = $this->actingAs($farmerUser)
            ->post(route('farmer.orders.status', $order->id), [
                'status' => 'ready_for_pickup',
            ]);

        $response->assertRedirect();

        Mail::assertSent(OrderStatusUpdateCustomerMail::class, function ($mail) use ($customer) {
            return $mail->hasTo($customer->email) && $mail->status === 'ready_for_pickup';
        });
    }

    public function test_admin_approving_farmer_dispatches_approval_email(): void
    {
        Mail::fake();

        $admin = User::create([
            'name' => 'Admin Boss',
            'username' => 'admin_boss',
            'email' => 'admin@platform.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'contact_number' => '0000000000',
            'address' => 'Admin HQ',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $farmerUser = User::create([
            'name' => 'New Farmer',
            'username' => 'new_farmer_99',
            'email' => 'newfarmer@example.com',
            'password' => bcrypt('password'),
            'role' => 'farmer',
            'contact_number' => '1112223333',
            'address' => 'Country Rd',
            'is_active' => false,
            'is_approved' => false,
        ]);

        $farmer = Farmer::create([
            'user_id' => $farmerUser->id,
            'stall_name' => 'Sunny Orchards',
            'contact_person' => 'Sam',
            'contact_number' => '1112223333',
            'address' => 'Booth 7',
            'is_approved' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.farmers.approve', $farmer->id));

        $response->assertRedirect();
        $this->assertTrue($farmer->fresh()->is_approved);

        Mail::assertSent(FarmerApprovedMail::class, function ($mail) use ($farmerUser) {
            return $mail->hasTo($farmerUser->email);
        });
    }
}
