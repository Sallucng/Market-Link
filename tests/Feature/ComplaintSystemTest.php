<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ComplaintSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected User $otherCustomer;
    protected User $farmerUser;
    protected Farmer $farmer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true, 'is_approved' => true]
        );

        $this->customer = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            ['name' => 'Customer User', 'password' => bcrypt('password'), 'role' => 'customer', 'is_active' => true, 'is_approved' => true]
        );

        $this->otherCustomer = User::firstOrCreate(
            ['email' => 'other_customer@test.com'],
            ['name' => 'Other Customer', 'password' => bcrypt('password'), 'role' => 'customer', 'is_active' => true, 'is_approved' => true]
        );

        $this->farmerUser = User::firstOrCreate(
            ['email' => 'farmer@test.com'],
            ['name' => 'Farmer User', 'password' => bcrypt('password'), 'role' => 'farmer', 'is_active' => true, 'is_approved' => true]
        );

        $market = Market::first() ?? Market::create([
            'name' => 'Test Market',
            'location' => '123 Market St',
            'city' => 'Metropolis',
            'address' => '123 Market St',
            'operating_hours' => 'Saturday 8am-2pm',
            'operating_days' => 'Saturday,Sunday',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $this->farmer = Farmer::firstOrCreate(
            ['user_id' => $this->farmerUser->id],
            [
                'market_id' => $market->id,
                'stall_name' => 'Sunny Fields Test Stall',
                'contact_person' => 'Sam Farmer',
                'contact_number' => '555-0199',
                'is_approved' => true,
            ]
        );
    }

    public function test_guest_cannot_file_complaint_or_view_complaints(): void
    {
        $response = $this->get('/customer/complaints');
        $response->assertRedirect('/login');

        $response = $this->get('/admin/complaints');
        $response->assertRedirect('/login');
    }

    public function test_customer_can_file_confidential_complaint(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->customer)->post('/customer/complaints', [
            'farmer_id' => $this->farmer->id,
            'complaint_type' => 'poor_quality',
            'subject' => 'Spoiled greens in weekly pickup',
            'description' => 'The leafy greens were wilted and smelled sour right after pickup from the stall.',
        ]);

        $this->assertDatabaseHas('complaints', [
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'subject' => 'Spoiled greens in weekly pickup',
            'status' => 'pending',
        ]);

        $complaint = Complaint::where('subject', 'Spoiled greens in weekly pickup')->first();
        $response->assertRedirect(route('customer.complaints.show', $complaint->id));
        $response->assertSessionHas('success');
    }

    public function test_customer_can_view_own_complaints_but_not_others(): void
    {
        $complaint = Complaint::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'complaint_type' => 'pricing_issue',
            'subject' => 'Pricing discrepancy issue',
            'description' => 'Detailed explanation of price problem.',
            'status' => 'pending',
        ]);

        // Own complaint: 200 OK
        $response = $this->actingAs($this->customer)->get('/customer/complaints/' . $complaint->id);
        $response->assertStatus(200);
        $response->assertSee('Pricing discrepancy issue');

        // Other customer accessing: 404 Not Found (scoped strictly to auth user)
        $response = $this->actingAs($this->otherCustomer)->get('/customer/complaints/' . $complaint->id);
        $response->assertStatus(404);
    }

    public function test_complaints_are_never_exposed_on_public_farmer_profile(): void
    {
        $complaint = Complaint::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'complaint_type' => 'unprofessional_conduct',
            'subject' => 'Secret confidential grievance text 98765',
            'description' => 'Highly confidential customer feedback that must remain private.',
            'status' => 'pending',
        ]);

        $response = $this->get('/farmers/' . $this->farmer->id);
        $response->assertStatus(200);
        $response->assertDontSee('Secret confidential grievance text 98765');
        $response->assertDontSee('Highly confidential customer feedback');
        $response->assertSee('Sign In to Report Stall');

        // Authenticated customer visits profile
        $customerResponse = $this->actingAs($this->customer)->get('/farmers/' . $this->farmer->id);
        $customerResponse->assertStatus(200);
        $customerResponse->assertDontSee('Secret confidential grievance text 98765');
        $customerResponse->assertSee('File Confidential Complaint');
    }

    public function test_farmer_cannot_access_admin_complaints_portal(): void
    {
        $response = $this->actingAs($this->farmerUser)->get('/admin/complaints');
        $response->assertStatus(403);
    }

    public function test_customer_cannot_access_admin_complaints_portal(): void
    {
        $response = $this->actingAs($this->customer)->get('/admin/complaints');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_and_moderate_complaint_status(): void
    {
        $complaint = Complaint::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'complaint_type' => 'unfulfilled_order',
            'subject' => 'Order missing from stall',
            'description' => 'The order was missing when I arrived.',
            'status' => 'pending',
        ]);

        // Admin index
        $response = $this->actingAs($this->admin)->get('/admin/complaints');
        $response->assertStatus(200);
        $response->assertSee('Order missing from stall');

        // Admin show
        $response = $this->actingAs($this->admin)->get('/admin/complaints/' . $complaint->id);
        $response->assertStatus(200);
        $response->assertSee('Order missing from stall');

        // Admin update status to resolved
        $response = $this->actingAs($this->admin)->post('/admin/complaints/' . $complaint->id . '/status', [
            'status' => 'resolved',
            'admin_notes' => 'Grower agreed to provide complimentary fresh basket on next visit.',
        ]);

        $response->assertRedirect(route('admin.complaints.show', $complaint->id));

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'status' => 'resolved',
            'admin_notes' => 'Grower agreed to provide complimentary fresh basket on next visit.',
            'resolved_by' => $this->admin->id,
        ]);
    }
}
