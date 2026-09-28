<?php

namespace Tests\Feature;

use App\Models\Farmer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_login_page_renders_continue_with_google_button(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee(route('auth.google'));
        $response->assertSee('Continue with Google');
    }

    public function test_register_page_renders_continue_with_google_button(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('Continue with Google as Customer');
    }

    public function test_redirect_to_google_informs_user_when_credentials_missing(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $response = $this->get(route('auth.google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_redirect_to_google_redirects_to_accounts_google_com_when_configured(): void
    {
        config([
            'services.google.client_id' => '877988720114-ud1bkiqal5do3bp7v96dk8ghbp8qpg74.apps.googleusercontent.com',
            'services.google.client_secret' => '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',
            'services.google.redirect' => 'http://localhost:8000/auth/google/callback',
        ]);

        $response = $this->get(route('auth.google'));

        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com/o/oauth2/auth', $response->headers->get('Location'));
        $this->assertStringContainsString('877988720114-ud1bkiqal5do3bp7v96dk8ghbp8qpg74.apps.googleusercontent.com', $response->headers->get('Location'));
    }

    public function test_existing_customer_can_login_via_google_callback(): void
    {
        $user = User::create([
            'name' => 'Sarah Shopper',
            'username' => 'sarah_shopper_test',
            'email' => 'sarah@example.com',
            'password' => bcrypt('Password@123'),
            'role' => 'customer',
            'contact_number' => '+15551234567',
            'address' => '789 Market Ave',
            'is_active' => true,
        ]);

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('google-customer-id-123');
        $abstractUser->shouldReceive('getEmail')->andReturn('sarah@example.com');
        $abstractUser->shouldReceive('getName')->andReturn('Sarah Shopper');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
        $this->assertEquals('google-customer-id-123', $user->fresh()->google_id);
    }

    public function test_existing_farmer_can_login_via_google_callback(): void
    {
        $user = User::create([
            'name' => 'Green Valley Grower',
            'username' => 'green_grower_test',
            'email' => 'grower@example.com',
            'password' => bcrypt('Password@123'),
            'role' => 'farmer',
            'contact_number' => '+15559876543',
            'address' => '100 Farm Lane',
            'is_active' => true,
            'is_approved' => true,
        ]);

        Farmer::create([
            'user_id' => $user->id,
            'stall_name' => 'Green Valley Orchard',
            'contact_person' => 'Green Valley Grower',
            'contact_number' => '+15559876543',
            'address' => '100 Farm Lane',
        ]);

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('google-farmer-id-456');
        $abstractUser->shouldReceive('getEmail')->andReturn('grower@example.com');
        $abstractUser->shouldReceive('getName')->andReturn('Green Valley Grower');
        $abstractUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('farmer.dashboard'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_admin_cannot_login_via_google_callback(): void
    {
        $admin = User::create([
            'name' => 'System Admin',
            'username' => 'sys_admin_test',
            'email' => 'admin@platform.com',
            'password' => bcrypt('Admin@123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('google-admin-id-999');
        $abstractUser->shouldReceive('getEmail')->andReturn('admin@platform.com');
        $abstractUser->shouldReceive('getName')->andReturn('System Admin');
        $abstractUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        // Admin must be rejected
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertFalse(Auth::check());
    }

    public function test_new_google_user_is_redirected_to_complete_profile_screen(): void
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('google-new-777');
        $abstractUser->shouldReceive('getEmail')->andReturn('newuser@gmail.com');
        $abstractUser->shouldReceive('getName')->andReturn('New Google User');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/new.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('auth.google.complete'));
        $response->assertSessionHas('google_auth_data');
        $this->assertFalse(Auth::check());
    }

    public function test_complete_profile_screen_renders_successfully(): void
    {
        $response = $this->withSession([
            'google_auth_data' => [
                'google_id' => 'google-new-777',
                'name' => 'Alice Fresh',
                'email' => 'alice.fresh@gmail.com',
                'avatar' => null,
                'role' => 'customer',
            ]
        ])->get(route('auth.google.complete'));

        $response->assertStatus(200);
        $response->assertSee('Almost Done!');
        $response->assertSee('alice.fresh@gmail.com');
        $response->assertSee('Alice Fresh');
        $response->assertSee('Contact Phone Number');
    }

    public function test_new_user_can_complete_profile_as_customer(): void
    {
        $response = $this->withSession([
            'google_auth_data' => [
                'google_id' => 'google-customer-new-1',
                'name' => 'Alice Customer',
                'email' => 'alice_customer@gmail.com',
                'avatar' => 'https://lh3.googleusercontent.com/alice.jpg',
                'role' => 'customer',
            ]
        ])->post(route('auth.google.complete.submit'), [
            'role' => 'customer',
            'username' => 'alice_customer_99',
            'contact_number' => '+15551112222',
            'address' => '456 Blossom Lane',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertTrue(Auth::check());

        $user = User::where('email', 'alice_customer@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
        $this->assertEquals('google-customer-new-1', $user->google_id);
        $this->assertEquals('+15551112222', $user->contact_number);
        $this->assertTrue($user->is_approved);
    }

    public function test_new_user_can_complete_profile_as_farmer(): void
    {
        $response = $this->withSession([
            'google_auth_data' => [
                'google_id' => 'google-farmer-new-2',
                'name' => 'Bob Farmer',
                'email' => 'bob_farmer@gmail.com',
                'avatar' => null,
                'role' => 'farmer',
            ]
        ])->post(route('auth.google.complete.submit'), [
            'role' => 'farmer',
            'stall_name' => 'Bob Berry Haven',
            'username' => 'bob_farmer_2026',
            'contact_number' => '+15553334444',
            'address' => '880 Country Road',
        ]);

        $response->assertRedirect(route('farmer.dashboard'));
        $this->assertTrue(Auth::check());

        $user = User::where('email', 'bob_farmer@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('farmer', $user->role);
        $this->assertFalse($user->is_approved); // Pending admin review

        $farmer = Farmer::where('user_id', $user->id)->first();
        $this->assertNotNull($farmer);
        $this->assertEquals('Bob Berry Haven', $farmer->stall_name);
    }

    public function test_cannot_complete_profile_with_admin_role(): void
    {
        $response = $this->withSession([
            'google_auth_data' => [
                'google_id' => 'google-hacker-999',
                'name' => 'Sneaky User',
                'email' => 'sneaky@gmail.com',
                'avatar' => null,
                'role' => 'customer',
            ]
        ])->post(route('auth.google.complete.submit'), [
            'role' => 'admin',
            'username' => 'sneaky_admin',
            'contact_number' => '+15559990000',
            'address' => 'Hacker Den',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'sneaky@gmail.com']);
    }
}
