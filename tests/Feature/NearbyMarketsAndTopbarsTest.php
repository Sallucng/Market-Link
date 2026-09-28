<?php

namespace Tests\Feature;

use App\Models\Farmer;
use App\Models\Market;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NearbyMarketsAndTopbarsTest extends TestCase
{
    use RefreshDatabase;

    public function test_nearby_markets_page_renders_successfully(): void
    {
        $response = $this->get(route('markets.nearby'));
        $response->assertStatus(200);
        $response->assertSee('Markets Near You');
        $response->assertSee('Use My Location');
        $response->assertSee('Search Radius');
    }

    public function test_nearby_markets_page_with_coordinates_returns_filtered_markets(): void
    {
        $market = Market::create([
            'name' => 'Downtown Fresh Hub',
            'address' => '100 Downtown Plaza',
            'city' => 'Metropolis',
            'operating_days' => 'Saturday, Sunday',
            'timings' => '08:00 AM - 02:00 PM',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'status' => 'active',
        ]);

        $farMarket = Market::create([
            'name' => 'Far Away Mountain Fair',
            'address' => '500 Mountain Trail',
            'city' => 'Highland',
            'operating_days' => 'Sunday',
            'timings' => '09:00 AM - 01:00 PM',
            'latitude' => 34.0522,
            'longitude' => -118.2437,
            'status' => 'active',
        ]);

        $response = $this->get(route('markets.nearby', ['lat' => 40.7128, 'lng' => -74.0060, 'radius' => 25]));
        $response->assertStatus(200);
        $response->assertSee('Downtown Fresh Hub');
        $response->assertDontSee('Far Away Mountain Fair');
    }

    public function test_nearby_markets_json_endpoint_returns_valid_data(): void
    {
        $market = Market::create([
            'name' => 'Central Plaza Market',
            'address' => '50 Main Street',
            'city' => 'Metropolis',
            'operating_days' => 'Saturday',
            'timings' => '08:00 AM - 01:00 PM',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'status' => 'active',
        ]);

        $response = $this->getJson(route('markets.nearby.json', ['lat' => 40.7128, 'lng' => -74.0060, 'radius' => 25]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'markets' => [
                '*' => [
                    'id',
                    'name',
                    'address',
                    'operating_days',
                    'timings',
                    'latitude',
                    'longitude',
                    'distance_km',
                    'farmer_count',
                    'url',
                ]
            ]
        ]);
        $response->assertJsonFragment(['name' => 'Central Plaza Market']);
    }

    public function test_admin_dashboard_topbar_renders_with_user_credentials(): void
    {
        $admin = User::create([
            'name' => 'Chief Admin',
            'email' => 'admin_test@marketlink.local',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Chief Admin');
        $response->assertSee('System Live');
        $response->assertSee('See as Normal User');
        $response->assertSee('MarketLink Management Console');
    }

    public function test_farmer_dashboard_topbar_renders_with_stall_indicator(): void
    {
        $market = Market::create([
            'name' => 'Green Valley Market',
            'address' => '12 Market Lane',
            'city' => 'Green Valley',
            'operating_days' => 'Saturday',
            'timings' => '08:00 AM - 02:00 PM',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $farmerUser = User::create([
            'name' => 'Farmer Joe',
            'email' => 'farmer_joe@marketlink.local',
            'password' => bcrypt('password'),
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => true,
        ]);

        Farmer::create([
            'user_id' => $farmerUser->id,
            'market_id' => $market->id,
            'stall_name' => 'Joe Organic Harvest',
            'contact_person' => 'Joe Miller',
            'contact_number' => '+15551234567',
            'address' => '12 Market Lane',
            'operating_days' => 'Saturday',
            'pickup_time_windows' => '08:00 AM - 11:00 AM',
            'cutoff_hours' => 12,
        ]);

        $response = $this->actingAs($farmerUser)->get(route('farmer.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Farmer Joe');
        $response->assertSee('Stall Live');
        $response->assertSee('See as Normal User');
        $response->assertSee('Green Valley Market');
    }

    public function test_customer_order_detail_view_renders_with_navbar(): void
    {
        $customer = User::create([
            'name' => 'Jane Shopper',
            'email' => 'jane_shopper@marketlink.local',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $market = Market::create([
            'name' => 'Market Square',
            'address' => '1 Square St',
            'city' => 'Metropolis',
            'operating_days' => 'Saturday',
            'timings' => '08:00 AM - 02:00 PM',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $farmerUser = User::create([
            'name' => 'Seller Tim',
            'email' => 'seller_tim@marketlink.local',
            'password' => bcrypt('password'),
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $farmer = Farmer::create([
            'user_id' => $farmerUser->id,
            'market_id' => $market->id,
            'stall_name' => 'Tim Harvest Stall',
            'contact_person' => 'Tim Vance',
            'contact_number' => '+15559876543',
            'address' => '1 Square St',
            'operating_days' => 'Saturday',
            'pickup_time_windows' => '08:00 AM - 11:00 AM',
            'cutoff_hours' => 12,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'farmer_id' => $farmer->id,
            'order_number' => 'ORD-TEST1234',
            'order_status' => 'placed',
            'pickup_date' => now()->addDays(2),
            'pickup_time_slot' => '09:00 AM - 11:00 AM',
            'total_amount' => 45.00,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.orders.show', $order->id));
        $response->assertStatus(200);
        $response->assertSee('id="navContent"', false);
        $response->assertSee('navbar-toggler', false);
        $response->assertSee('Jane Shopper');
        $response->assertSee('ORD-TEST1234');
    }
}
