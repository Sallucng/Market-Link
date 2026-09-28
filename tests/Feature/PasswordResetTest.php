<?php

namespace Tests\Feature;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSee('Forgot Password?');
        $response->assertSee('Send Password Reset Link');
    }

    public function test_login_screen_contains_forgot_password_link(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Forgot password?');
        $response->assertSee(route('password.request'));
    }

    public function test_password_reset_link_can_be_requested_for_existing_user(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Demo Shopper',
            'username' => 'demo_shopper',
            'email' => 'shopper@example.com',
            'password' => Hash::make('OldPassword@123'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'shopper@example.com',
        ]);

        $response->assertSessionHas('status');
        Mail::assertSent(ResetPasswordMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) &&
                   $mail->user->id === $user->id &&
                   str_contains($mail->resetUrl, '/reset-password/');
        });
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $response = $this->get(route('password.reset', [
            'token' => 'sample-test-token',
            'email' => 'shopper@example.com',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Create New Password');
        $response->assertSee('shopper@example.com');
        $response->assertSee('Reset Password');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::create([
            'name' => 'Green Valley Farmer',
            'username' => 'green_valley_farmer',
            'email' => 'farmer@example.com',
            'password' => Hash::make('OldPassword@123'),
            'role' => 'farmer',
            'is_active' => true,
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'farmer@example.com',
            'password' => 'NewSecurePassword@2026',
            'password_confirmation' => 'NewSecurePassword@2026',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('NewSecurePassword@2026', $user->fresh()->password));
        $this->assertFalse(Hash::check('OldPassword@123', $user->fresh()->password));
    }

    public function test_password_cannot_be_reset_with_invalid_token(): void
    {
        $user = User::create([
            'name' => 'Demo User',
            'username' => 'demo_user',
            'email' => 'user@example.com',
            'password' => Hash::make('OldPassword@123'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        $response = $this->from(route('password.reset', ['token' => 'invalid-token', 'email' => 'user@example.com']))
            ->post(route('password.update'), [
                'token' => 'invalid-token',
                'email' => 'user@example.com',
                'password' => 'NewSecurePassword@2026',
                'password_confirmation' => 'NewSecurePassword@2026',
            ]);

        $response->assertRedirect(route('password.reset', ['token' => 'invalid-token', 'email' => 'user@example.com']));
        $response->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('OldPassword@123', $user->fresh()->password));
    }
}
