<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V1ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private string $adminToken;
    private User $farmerUser;
    private string $farmerToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@marketlink.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
            'status'   => 'active',
        ]);
        $this->adminToken = $this->adminUser->createToken('admin-v1')->plainTextToken;

        $this->farmerUser = User::create([
            'name'     => 'Approved Farmer',
            'email'    => 'farmer@marketlink.com',
            'password' => bcrypt('password123'),
            'role'     => 'farmer',
            'status'   => 'active',
        ]);
        $this->farmerUser->farmerProfile()->create([
            'business_name' => 'Green Fields Farm',
            'address'       => '123 Meadow Lane',
            'is_approved'   => true,
        ]);
        $this->farmerToken = $this->farmerUser->createToken('farmer-v1')->plainTextToken;
    }

    public function test_v1_admin_rejects_unauthenticated_request(): void
    {
        $response = $this->getJson('/api/v1/admin/dashboard');
        $response->assertStatus(401);
    }

    public function test_v1_admin_rejects_farmer_user(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->getJson('/api/v1/admin/dashboard');

        $response->assertStatus(403)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Unauthorized access. Platform administrator privileges required.');
    }

    public function test_v1_admin_allows_admin_user(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/admin/dashboard');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');
    }

    public function test_v1_farmer_rejects_unauthenticated_request(): void
    {
        $response = $this->getJson('/api/v1/farmer/profile');
        $response->assertStatus(401);
    }

    public function test_v1_farmer_rejects_admin_user(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/farmer/profile');

        $response->assertStatus(403)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Unauthorized access. Farmer account credentials required.');
    }

    public function test_v1_farmer_allows_approved_farmer_user(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->farmerToken}")
            ->getJson('/api/v1/farmer/profile');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');
    }
}
