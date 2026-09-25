<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Validates SRS §1.7 Non-Functional Requirements:
 * - Security & Access Control (RBAC, Authentication Gates)
 * - Safe to Use (Sanitized MIME file downloads, no malicious behavior)
 * - Operability & Reliability (Custom 404/403 Error handling)
 * - Performance & Scalability (Paginated catalogues, eager relations)
 * - Accessibility (Skip links, semantic landmarks)
 */
class NonFunctionalRequirementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $farmerUser;
    protected Farmer $farmerProfile;
    protected User $customerUser;
    protected Market $testMarket;
    protected Category $testCategory;
    protected Product $testProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testMarket = Market::create([
            'name' => 'Metro Plaza Farmers Market',
            'address' => '100 Central Square',
            'city' => 'Metropolis',
            'operating_days' => 'Saturday, Sunday',
            'timings' => '08:00 AM - 02:00 PM',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $this->testCategory = Category::create([
            'name' => 'Fresh Vegetables',
            'description' => 'Locally harvested organic vegetables',
        ]);

        $this->adminUser = User::create([
            'name' => 'System Administrator',
            'username' => 'sysadmin',
            'email' => 'admin@marketlink.local',
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);

        $this->farmerUser = User::create([
            'name' => 'Green Fields Grower',
            'username' => 'greenfields',
            'email' => 'grower@marketlink.local',
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);

        $this->farmerProfile = Farmer::create([
            'user_id' => $this->farmerUser->id,
            'market_id' => $this->testMarket->id,
            'stall_name' => 'Green Fields Stall',
            'contact_person' => 'Farmer John',
            'contact_number' => '555-0200',
            'address' => 'Stall 14, Metro Plaza',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'operating_days' => 'Saturday, Sunday',
            'pickup_time_windows' => '08:00 AM - 11:00 AM',
            'cutoff_hours' => 4,
        ]);

        $this->customerUser = User::create([
            'name' => 'Conscious Shopper',
            'username' => 'shopper1',
            'email' => 'shopper@marketlink.local',
            'role' => 'customer',
            'is_active' => true,
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);

        $this->testProduct = Product::create([
            'farmer_id' => $this->farmerProfile->id,
            'category_id' => $this->testCategory->id,
            'name' => 'Organic Rainbow Chard',
            'description' => 'Tender, fresh harvest chard.',
            'price' => 3.75,
            'stock_quantity' => 25,
            'weekly_recurring_stock' => 25,
            'unit' => 'bunch',
            'is_available' => true,
            'is_sold_out' => false,
        ]);
    }

    /**
     * Requirement: Security & RBAC
     * Unauthenticated guests must be redirected to login from private dashboards.
     */
    public function test_security_guests_cannot_access_protected_portals(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('farmer.dashboard'))->assertRedirect(route('login'));
        $this->get(route('customer.dashboard'))->assertRedirect(route('login'));
    }

    /**
     * Requirement: Security & RBAC
     * Customers cannot access farmer or admin protected backoffices.
     */
    public function test_security_customers_cannot_access_admin_or_farmer_portals(): void
    {
        $adminRes = $this->actingAs($this->customerUser)->get(route('admin.dashboard'));
        $this->assertTrue(in_array($adminRes->status(), [403, 302]));

        $farmerRes = $this->actingAs($this->customerUser)->get(route('farmer.dashboard'));
        $this->assertTrue(in_array($farmerRes->status(), [403, 302]));
    }

    /**
     * Requirement: Security
     * Inactive (deactivated) accounts cannot log in.
     */
    public function test_security_inactive_accounts_are_blocked_from_login(): void
    {
        $this->customerUser->update(['is_active' => false]);

        $response = $this->post('/login', [
            'email' => $this->customerUser->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    /**
     * Requirement: Security & Data Integrity
     * Unapproved farmer products are not visible in public catalogues.
     */
    public function test_security_unapproved_farmer_products_hidden_from_public_catalog(): void
    {
        $pendingUser = User::create([
            'name' => 'Pending Vendor',
            'username' => 'pending_vendor',
            'email' => 'pending@marketlink.local',
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => false,
            'password' => bcrypt('password'),
        ]);

        $pendingProfile = Farmer::create([
            'user_id' => $pendingUser->id,
            'market_id' => $this->testMarket->id,
            'stall_name' => 'Pending Organic Stalls',
            'contact_person' => 'Pending Owner',
            'contact_number' => '555-9999',
            'address' => 'Stall 99',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $secretProduct = Product::create([
            'farmer_id' => $pendingProfile->id,
            'category_id' => $this->testCategory->id,
            'name' => 'Unapproved Secret Honeydew',
            'price' => 5.00,
            'stock_quantity' => 10,
            'unit' => 'each',
            'is_available' => true,
        ]);

        $catalogResponse = $this->get(route('products.index'));
        $catalogResponse->assertOk();
        $catalogResponse->assertDontSee('Unapproved Secret Honeydew');
    }

    /**
     * Requirement: Safe to Use
     * Validates that receipt PDF downloads and CSV reports deliver strictly sanitized MIME types.
     */
    public function test_safe_to_use_downloads_deliver_clean_mime_types(): void
    {
        $order = Order::create([
            'customer_id' => $this->customerUser->id,
            'farmer_id' => $this->farmerProfile->id,
            'market_id' => $this->testMarket->id,
            'order_number' => 'ML-SAFE-001',
            'order_status' => 'placed',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time_slot' => '08:00 AM - 10:00 AM',
            'total_amount' => 7.50,
            'payment_method' => 'pay_at_pickup',
        ]);

        // Customer receipt PDF download
        $pdfRes = $this->actingAs($this->customerUser)->get(route('customer.orders.receipt', $order->id));
        $pdfRes->assertOk();
        $this->assertStringContainsString('application/pdf', $pdfRes->headers->get('content-type'));

        // Admin CSV report export
        $csvRes = $this->actingAs($this->adminUser)->get(route('admin.reports.export'));
        $csvRes->assertOk();
        $this->assertStringContainsString('text/csv', $csvRes->headers->get('content-type'));
    }

    /**
     * Requirement: Operability & Reliability
     * Custom 404 error page renders for missing records.
     */
    public function test_operability_custom_404_page_renders_for_missing_resource(): void
    {
        $response = $this->get('/products/999999');
        $response->assertStatus(404);
        $response->assertSee('Harvest Item or Page Not Found');
        $response->assertSee('Return Home');
    }

    /**
     * Requirement: Performance & Scalability
     * Large product catalog is paginated to prevent memory exhaustion.
     */
    public function test_performance_catalog_is_paginated(): void
    {
        $response = $this->get(route('products.index'));
        $response->assertOk();
        $response->assertViewHas('products');
        
        $paginator = $response->viewData('products');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $paginator);
    }

    /**
     * Requirement: Accessibility
     * Layout contains semantic skip-link and main content landmark.
     */
    public function test_accessibility_landmarks_and_skip_link_present(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSee('Skip to main content');
        $response->assertSee('id="main-content"', false);
        $response->assertSee('role="main"', false);
    }
}
