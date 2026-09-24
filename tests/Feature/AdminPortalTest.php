<?php

namespace Tests\Feature;

use App\Models\Farmer;
use App\Models\Market;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'System Admin',
            'username' => 'sysadmin',
            'email' => 'admin@test.local',
            'contact_number' => '555-9999',
            'address' => 'HQ',
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);
    }

    public function test_admin_can_view_dashboard_and_manage_markets(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Create market
        $marketResponse = $this->actingAs($this->adminUser)->post(route('admin.markets.store'), [
            'name' => 'Riverside Weekend Market',
            'address' => '200 River Rd',
            'city' => 'Brooklyn',
            'operating_days' => 'Sunday',
            'timings' => '09:00 AM - 02:00 PM',
            'latitude' => 40.7000,
            'longitude' => -73.9900,
        ]);

        $marketResponse->assertRedirect(route('admin.markets.index'));
        $this->assertDatabaseHas('markets', [
            'name' => 'Riverside Weekend Market',
        ]);
    }

    public function test_admin_farmer_approval_gate(): void
    {
        $unapprovedUser = User::create([
            'name' => 'New Farmer',
            'username' => 'newfarmer',
            'email' => 'newfarmer@test.local',
            'contact_number' => '555-5555',
            'address' => 'Farmland',
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => false,
            'password' => bcrypt('password'),
        ]);

        $farmer = Farmer::create([
            'user_id' => $unapprovedUser->id,
            'stall_name' => 'Pending Stall',
            'contact_person' => 'New Farmer',
            'contact_number' => '555-5555',
            'address' => 'Stall 9',
            'operating_days' => 'Sunday',
            'pickup_time_windows' => '09:00 AM - 11:00 AM',
            'cutoff_hours' => 2,
        ]);

        // Approve
        $approveResponse = $this->actingAs($this->adminUser)->post(route('admin.farmers.approve', $farmer->id));
        $approveResponse->assertRedirect();

        $unapprovedUser->refresh();
        $this->assertTrue((bool)$unapprovedUser->is_approved);
    }

    public function test_admin_dashboard_renders_analytics_charts_and_sidebar(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('marketRevenueBarChart');
        $response->assertSee('orderStatusPieChart');
        $response->assertSee('categoryDistributionChart');
        $response->assertSee('See as Normal User');
        $response->assertSee('admin-sidebar');
    }

    public function test_admin_can_view_reports_with_charts(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.index'));
        $response->assertStatus(200);
        $response->assertSee('reportMarketRevenueChart');
        $response->assertSee('reportOrderStatusChart');
        $response->assertSee('reportTopFarmersChart');
        $response->assertSee('See as Normal User');
    }
}
