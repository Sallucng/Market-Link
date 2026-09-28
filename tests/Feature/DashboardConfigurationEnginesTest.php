<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardConfigurationEnginesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $farmerUser;
    protected Farmer $farmer;
    protected User $customer;
    protected Market $market;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'username' => 'admin_test',
            'name' => 'Admin Officer',
            'email' => 'admin.test@marketlink.local',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $this->market = Market::create([
            'name' => 'City Green Market',
            'address' => '100 Market St',
            'city' => 'Metropolis',
            'operating_days' => 'Saturday, Sunday',
            'timings' => '08:00 AM - 02:00 PM',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $this->farmerUser = User::create([
            'username' => 'grower_test',
            'name' => 'Fresh Grower',
            'email' => 'grower.test@marketlink.local',
            'password' => bcrypt('password'),
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $this->farmer = Farmer::create([
            'user_id' => $this->farmerUser->id,
            'market_id' => $this->market->id,
            'stall_name' => 'Highland Organic Stall',
            'contact_person' => 'Fresh Grower',
            'contact_number' => '+15551234567',
            'cutoff_hours' => 2,
            'settings' => [
                'stall_open' => true,
                'auto_accept_orders' => true,
                'min_pickup_lead_minutes' => 60,
            ],
        ]);

        $this->customer = User::create([
            'username' => 'shopper_test',
            'name' => 'Alice Shopper',
            'email' => 'shopper.test@marketlink.local',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'is_active' => true,
            'is_approved' => true,
            'preferences' => [
                'notify_order_status_sms' => true,
                'notify_weekly_harvest_digest' => true,
                'pickup_reminder_timing' => '2h',
                'dietary_preferences' => ['organic', 'pesticide_free'],
            ],
        ]);
    }

    public function test_admin_can_access_and_view_settings_engine(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Platform Configuration');
        $response->assertSee('Market & Pre-Order Rules', false);
        $response->assertSee('System Alerts & Automation', false);
        $response->assertSee('Grower & Vendor Policies', false);
        $response->assertSee('Storefront Appearance & Alerts', false);
    }

    public function test_admin_can_update_settings(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'settings' => [
                'default_cutoff_hours' => '4',
                'max_preorder_days' => '10',
                'site_title' => 'Updated MarketLink Platform',
            ],
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertEquals(4, Setting::get('default_cutoff_hours'));
        $this->assertEquals(10, Setting::get('max_preorder_days'));
        $this->assertEquals('Updated MarketLink Platform', Setting::get('site_title'));
    }

    public function test_admin_quick_toggle_ajax(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('admin.settings.quick-toggle'), [
            'key' => 'auto_approve_farmers',
            'value' => '1',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertTrue(Setting::get('auto_approve_farmers'));
    }

    public function test_farmer_can_access_and_view_operations_settings(): void
    {
        $response = $this->actingAs($this->farmerUser)->get(route('farmer.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Stall Operations & Configurations', false);
        $response->assertSee('Pre-Order Rules & Cutoff', false);
        $response->assertSee('Stall Pickup Time Windows', false);
        $response->assertSee('Inventory Alerts & Notifications', false);
        $response->assertSee('Stall Status & Holiday Mode', false);
    }

    public function test_farmer_can_update_operational_settings(): void
    {
        $response = $this->actingAs($this->farmerUser)->post(route('farmer.settings.update'), [
            'cutoff_hours' => 3,
            'pickup_time_windows' => "08:00 AM - 10:00 AM\n10:30 AM - 12:30 PM",
            'stall_open' => '1',
            'auto_accept_orders' => '1',
            'min_pickup_lead_minutes' => 45,
            'low_stock_alert' => '1',
            'low_stock_threshold' => 10,
        ]);

        $response->assertRedirect(route('farmer.settings.index'));
        $this->farmer->refresh();
        $this->assertEquals(3, $this->farmer->cutoff_hours);
        $this->assertEquals(45, $this->farmer->getSetting('min_pickup_lead_minutes'));
        $this->assertEquals(10, $this->farmer->getSetting('low_stock_threshold'));
    }

    public function test_farmer_quick_toggle_ajax(): void
    {
        $response = $this->actingAs($this->farmerUser)->postJson(route('farmer.settings.quick-toggle'), [
            'key' => 'stall_open',
            'value' => '0',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->farmer->refresh();
        $this->assertFalse($this->farmer->getSetting('stall_open'));
    }

    public function test_customer_can_access_and_view_preferences_settings(): void
    {
        $response = $this->actingAs($this->customer)->get(route('customer.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Account Preferences');
        $response->assertSee('Shopper Profile');
        $response->assertSee('Preferred Venue & Pickup Alerts', false);
        $response->assertSee('Notification Triggers & Privacy Options', false);
    }

    public function test_customer_can_update_preferences(): void
    {
        $response = $this->actingAs($this->customer)->post(route('customer.settings.update'), [
            'name' => 'Alice Updated',
            'contact_number' => '+15559998888',
            'address' => '789 Garden Way',
            'pickup_reminder_timing' => '1h',
            'dietary_preferences' => ['organic', 'non_gmo'],
            'notify_order_status_sms' => '1',
            'display_review_anonymously' => '1',
        ]);

        $response->assertRedirect(route('customer.settings.index'));
        $this->customer->refresh();
        $this->assertEquals('Alice Updated', $this->customer->name);
        $this->assertEquals('1h', $this->customer->getPreference('pickup_reminder_timing'));
        $this->assertTrue($this->customer->getPreference('display_review_anonymously'));
        $this->assertContains('non_gmo', $this->customer->getPreference('dietary_preferences'));
    }

    public function test_customer_quick_toggle_ajax(): void
    {
        $response = $this->actingAs($this->customer)->postJson(route('customer.settings.quick-toggle'), [
            'key' => 'notify_order_status_sms',
            'value' => '0',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->customer->refresh();
        $this->assertFalse($this->customer->getPreference('notify_order_status_sms'));

        // Test dietary toggle
        $dietResponse = $this->actingAs($this->customer)->postJson(route('customer.settings.quick-toggle'), [
            'key' => 'dietary_toggle',
            'value' => 'toggle',
            'tag' => 'heirloom',
        ]);

        $dietResponse->assertStatus(200);
        $this->customer->refresh();
        $this->assertContains('heirloom', $this->customer->getPreference('dietary_preferences'));
    }

    public function test_unauthorized_access_is_prevented(): void
    {
        // Customer cannot access admin settings
        $this->actingAs($this->customer)->get(route('admin.settings.index'))->assertStatus(403);

        // Farmer cannot access admin settings
        $this->actingAs($this->farmerUser)->get(route('admin.settings.index'))->assertStatus(403);

        // Customer cannot access farmer settings
        $this->actingAs($this->customer)->get(route('farmer.settings.index'))->assertStatus(403);

        // Guest cannot access any settings
        auth()->logout();
        $this->get(route('admin.settings.index'))->assertRedirect(route('login'));
        $this->get(route('farmer.settings.index'))->assertRedirect(route('login'));
        $this->get(route('customer.settings.index'))->assertRedirect(route('login'));
    }
}
