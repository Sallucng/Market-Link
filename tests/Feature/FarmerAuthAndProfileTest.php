<?php

namespace Tests\Feature;

use App\Models\Market;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmerAuthAndProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_farmer_can_register_with_business_details(): void
    {
        $response = $this->postJson('/api/farmer/register', [
            'name' => 'Bob Miller',
            'email' => 'bob@millerfarms.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+15554443322',
            'farm_name' => 'Miller Berry Farms',
            'bio' => 'Fresh berries grown with care.',
            'stall_number' => 'B-12',
            'business_license' => 'LIC-778899',
            'address' => '789 Berry Lane',
            'city' => 'Watsonville',
            'state' => 'CA',
            'postal_code' => '95076',
            'latitude' => 36.9102,
            'longitude' => -121.7569,
            'operating_days' => ['Saturday', 'Sunday'],
            'pickup_window_start' => '08:00',
            'pickup_window_end' => '12:00',
            'order_cutoff_time' => '17:00',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.user.role', 'farmer')
            ->assertJsonPath('data.user.status', 'pending')
            ->assertJsonPath('data.user.farmer_profile.farm_name', 'Miller Berry Farms')
            ->assertJsonPath('data.user.farmer_profile.approval_status', 'pending');

        $this->assertDatabaseHas('users', ['email' => 'bob@millerfarms.com', 'role' => 'farmer']);
        $this->assertDatabaseHas('farmer_profiles', ['business_name' => 'Miller Berry Farms']);
    }

    public function test_farmer_can_login_and_retrieve_profile(): void
    {
        $this->postJson('/api/farmer/register', [
            'name' => 'Sam Green',
            'email' => 'sam@greenacres.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'farm_name' => 'Green Acres',
            'address' => '123 Farm Rd',
            'city' => 'Salinas',
            'state' => 'CA',
            'postal_code' => '93901',
        ]);

        $loginResponse = $this->postJson('/api/farmer/login', [
            'email' => 'sam@greenacres.com',
            'password' => 'Password123!',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['user', 'token']]);

        $token = $loginResponse->json('data.token');

        $profileResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/farmer/profile');

        $profileResponse->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.farm_name', 'Green Acres');
    }

    public function test_farmer_can_update_profile_and_associate_with_market(): void
    {
        $market = Market::create([
            'name' => 'Downtown Farmers Market',
            'location' => 'Downtown Square',
            'address' => '1st Street',
            'operating_days' => ['Saturday'],
            'open_time' => '08:00',
            'close_time' => '13:00',
            'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'Tom Taylor',
            'email' => 'tom@taylorfarm.com',
            'password' => bcrypt('Password123!'),
            'role' => 'farmer',
            'status' => 'active',
        ]);

        $profile = $user->farmerProfile()->create([
            'farm_name' => 'Taylor Family Farm',
            'address' => '500 Country Rd',
            'city' => 'Salinas',
            'state' => 'CA',
            'postal_code' => '93901',
            'approval_status' => 'approved',
        ]);

        $token = $user->createToken('test')->plainTextToken;

        // Update profile
        $updateResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/farmer/profile', [
                'farm_name' => 'Taylor Family Organic Farm',
                'bio' => 'Updated bio information',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.farm_name', 'Taylor Family Organic Farm');

        // Join market
        $joinResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/farmer/markets/join', [
                'market_id' => $market->id,
                'assigned_stall' => 'Stall 4A',
            ]);

        $joinResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('farmer_market', [
            'farmer_profile_id' => $profile->id,
            'market_id' => $market->id,
            'assigned_stall' => 'Stall 4A',
        ]);

        // Leave market
        $leaveResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/farmer/markets/{$market->id}/leave");

        $leaveResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('farmer_market', [
            'farmer_profile_id' => $profile->id,
            'market_id' => $market->id,
        ]);
    }
}
