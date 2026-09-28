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

class AnalyticsPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Farmer dashboard query executes efficiently under 100+ orders without N+1 bottlenecks.
     */
    public function test_farmer_dashboard_query_executes_efficiently_under_large_order_volume(): void
    {
        $farmerUser = User::create([
            'name'     => 'High Volume Farmer',
            'email'    => 'highvolume@farm.com',
            'password' => bcrypt('Secret123!'),
            'role'     => 'farmer',
            'status'   => 'active',
        ]);

        $profile = $farmerUser->farmerProfile()->create([
            'business_name'     => 'High Yield Organic Orchards',
            'stall_number'      => 'Stall #88',
            'address'           => '888 Harvest Rd',
            'operating_days'    => ['Saturday', 'Sunday'],
            'pickup_start_time' => '08:00',
            'pickup_end_time'   => '14:00',
            'is_approved'       => true,
        ]);

        $token = $farmerUser->createToken('perf-token')->plainTextToken;

        $category = Category::create([
            'name'      => 'Orchard Fruits',
            'slug'      => 'orchard-fruits',
            'is_active' => true,
        ]);

        // Seed 3 distinct products for this farmer
        $products = [];
        for ($p = 1; $p <= 3; $p++) {
            $products[] = Product::create([
                'farmer_profile_id' => $profile->id,
                'category_id'       => $category->id,
                'name'              => "Premium Fruit Variety #{$p}",
                'slug'              => "fruit-variety-{$p}-" . uniqid(),
                'price'             => 5.00 * $p,
                'unit'              => 'box',
                'stock_quantity'    => 500,
                'weekly_quota'      => 500,
                'is_available'      => true,
            ]);
        }

        $customer = User::create([
            'name'     => 'Frequent Customer',
            'email'    => 'frequent@customer.com',
            'password' => bcrypt('Pass123!'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        $market = Market::create([
            'name'           => 'City Center Farmers Market',
            'location'       => 'City Plaza',
            'address'        => '100 Plaza Way',
            'operating_days' => ['Saturday', 'Sunday'],
            'open_time'      => '08:00',
            'close_time'     => '14:00',
            'status'         => 'active',
        ]);

        // Seed 105 total orders: 75 completed, 20 pending, 10 declined
        $orderData = [];
        $itemsData = [];
        $totalCompletedSales = 0;

        for ($i = 1; $i <= 105; $i++) {
            $status = $i <= 75 ? 'completed' : ($i <= 95 ? 'pending' : 'declined');
            $prod = $products[$i % 3];
            $qty = ($i % 3) + 1;
            $subtotal = round($prod->price * $qty, 2);

            if ($status === 'completed') {
                $totalCompletedSales += $subtotal;
            }

            $order = Order::create([
                'customer_id'       => $customer->id,
                'farmer_profile_id' => $profile->id,
                'market_id'         => $market->id,
                'total_amount'      => $subtotal,
                'status'            => $status,
                'pickup_slot'       => '09:00 AM - 10:00 AM',
                'cutoff_time'       => now()->addHours(12),
            ]);

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $prod->id,
                'quantity'   => $qty,
                'unit_price' => $prod->price,
                'subtotal'   => $subtotal,
            ]);
        }

        $this->assertEquals(105, Order::where('farmer_profile_id', $profile->id)->count());

        // Enable query log to count SQL executions
        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/farmer/dashboard/stats');

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        // Confirm O(1) query complexity: Query count is strictly bounded (<= 15 queries for 105 orders, NOT 105+ queries)
        $this->assertLessThanOrEqual(15, $queryCount, "Expected O(1) queries for dashboard, but found {$queryCount} queries.");

        // Confirm arithmetic accuracy of aggregated analytics
        $response->assertJsonPath('data.total_orders', 105);
        $response->assertJsonPath('data.pending_orders', 20);
        $response->assertJsonPath('data.completed_orders', 75);
        $response->assertJsonPath('data.declined_orders', 10);
        $this->assertEquals((float)$totalCompletedSales, (float)$response->json('data.revenue_summary.completed_revenue'));

        // Best selling products returned via SQL group by
        $response->assertJsonStructure(['data' => ['best_selling_products']]);
        $this->assertNotEmpty($response->json('data.best_selling_products'));
    }

    /**
     * 2. Admin dashboard statistics returns accurate platform-wide counts.
     */
    public function test_admin_dashboard_statistics_returns_accurate_platform_counts(): void
    {
        $admin = User::create([
            'name'     => 'Head Administrator',
            'email'    => 'admin@marketlink.com',
            'password' => bcrypt('AdminSecret123!'),
            'role'     => 'admin',
            'status'   => 'active',
        ]);
        $adminToken = $admin->createToken('admin-token')->plainTextToken;

        // Seed 3 Approved Farmers and 2 Pending Farmers
        for ($f = 1; $f <= 5; $f++) {
            $isApproved = $f <= 3;
            $farmer = User::create([
                'name'     => "Farmer {$f}",
                'email'    => "farmer{$f}@marketlink.com",
                'password' => bcrypt('Farmer123!'),
                'role'     => 'farmer',
                'status'   => $isApproved ? 'active' : 'pending',
            ]);
            $farmer->farmerProfile()->create([
                'business_name'     => "Farm {$f}",
                'address'           => "Address {$f}",
                'operating_days'    => ['Saturday'],
                'is_approved'       => $isApproved,
                'approval_status'   => $isApproved ? 'approved' : 'pending',
            ]);
        }

        // Seed 4 Customers
        for ($c = 1; $c <= 4; $c++) {
            User::create([
                'name'     => "Customer {$c}",
                'email'    => "customer{$c}@marketlink.com",
                'password' => bcrypt('Cust123!'),
                'role'     => 'customer',
                'status'   => 'active',
            ]);
        }

        // Seed 2 Active Markets and 1 Inactive Market
        Market::create([
            'name'           => 'Active Market Alpha',
            'location'       => 'North',
            'address'        => '100 North Rd',
            'operating_days' => ['Saturday'],
            'open_time'      => '08:00',
            'close_time'     => '14:00',
            'status'         => 'active',
        ]);
        Market::create([
            'name'           => 'Active Market Beta',
            'location'       => 'South',
            'address'        => '200 South Rd',
            'operating_days' => ['Sunday'],
            'open_time'      => '09:00',
            'close_time'     => '15:00',
            'status'         => 'active',
        ]);
        Market::create([
            'name'           => 'Inactive Seasonal Market',
            'location'       => 'East',
            'address'        => '300 East Rd',
            'operating_days' => ['Sunday'],
            'open_time'      => '09:00',
            'close_time'     => '15:00',
            'status'         => 'inactive',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$adminToken}")
            ->getJson('/api/v1/admin/dashboard/stats');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        // Verify accurate counts across boundaries
        $data = $response->json('data');

        // Total farmers = 5 (3 approved, 2 pending)
        $this->assertEquals(5, $data['farmers']['total']);
        $this->assertEquals(3, $data['farmers']['approved']);
        $this->assertEquals(2, $data['farmers']['pending_approval']);

        // Total customers = 4
        $this->assertEquals(4, $data['customers']['total']);

        // Markets = 3 total, 2 active
        $this->assertEquals(3, $data['markets']['total']);
        $this->assertEquals(2, $data['markets']['active']);
    }
}
