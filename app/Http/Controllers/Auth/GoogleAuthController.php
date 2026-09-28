<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeUserMail;
use App\Models\Farmer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(Request $request)
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->with('error', 'Google Sign-In is pending credentials configuration. Please provide GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in .env.');
        }

        // Store role preference if user clicked "Continue with Google as Farmer/Customer"
        if ($request->has('role') && in_array($request->query('role'), ['customer', 'farmer'])) {
            session(['google_intended_role' => $request->query('role')]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('login')->with('error', 'Google sign-in was cancelled.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Unable to authenticate with Google. Please try again or sign in with your password.');
        }

        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();

        if (empty($email)) {
            return redirect()->route('login')->with('error', 'No verified email returned by Google account.');
        }

        // Check if an account already exists with this email or google_id
        $user = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        // 1. Existing user
        if ($user) {
            // STRICT SECURITY RULE: Admin accounts can NEVER log in with Google
            if ($user->isAdmin()) {
                return redirect()->route('login')->with('error', 'Admin accounts cannot sign in via Google. Please log in directly using your administrator username and password.');
            }

            if (!$user->is_active) {
                return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact platform administrators.');
            }

            // Link google_id and avatar if missing
            $dirty = false;
            if (!$user->google_id) {
                $user->google_id = $googleId;
                $dirty = true;
            }
            if (!$user->avatar && $googleUser->getAvatar()) {
                $user->avatar = $googleUser->getAvatar();
                $dirty = true;
            }
            if ($dirty) {
                $user->save();
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            if ($user->isFarmer()) {
                return redirect()->route('farmer.dashboard')->with('success', 'Welcome back! Signed in with Google.');
            }

            return redirect()->route('customer.dashboard')->with('success', 'Welcome back! Signed in with Google.');
        }

        // 2. New user: Store Google profile data in session and prompt for missing profile details (phone, address, role)
        session([
            'google_auth_data' => [
                'google_id' => $googleId,
                'email' => $email,
                'name' => $googleUser->getName() ?: 'MarketLink User',
                'avatar' => $googleUser->getAvatar(),
                'role' => session('google_intended_role', 'customer'),
            ]
        ]);

        return redirect()->route('auth.google.complete');
    }

    /**
     * Show the profile completion form for new Google users.
     */
    public function showCompleteProfile()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $googleData = session('google_auth_data');

        if (!$googleData) {
            return redirect()->route('login')->with('error', 'Session expired. Please sign in with Google again.');
        }

        // Generate a clean default username suggestion from Google name
        $suggestedUsername = Str::slug($googleData['name'], '_');
        if (empty($suggestedUsername)) {
            $suggestedUsername = 'user_' . Str::random(5);
        }
        if (User::where('username', $suggestedUsername)->exists()) {
            $suggestedUsername .= '_' . rand(10, 99);
        }

        return view('auth.google-complete-profile', [
            'googleData' => $googleData,
            'suggestedUsername' => $suggestedUsername,
        ]);
    }

    /**
     * Complete registration and create the Customer or Farmer account.
     */
    public function completeProfile(Request $request)
    {
        $googleData = session('google_auth_data');

        if (!$googleData) {
            return redirect()->route('login')->with('error', 'Registration session expired. Please try signing in with Google again.');
        }

        // Validate remaining required fields - STRICTLY customer or farmer only!
        $validated = $request->validate([
            'role' => 'required|in:customer,farmer',
            'username' => 'required|string|max:50|unique:users,username',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'stall_name' => 'nullable|string|max:100',
        ]);

        $isFarmer = $validated['role'] === 'farmer';

        $user = User::create([
            'name' => $googleData['name'],
            'username' => $validated['username'],
            'email' => $googleData['email'],
            'contact_number' => $validated['contact_number'],
            'address' => $validated['address'],
            'role' => $validated['role'],
            'is_active' => true,
            'is_approved' => !$isFarmer, // Farmers require admin approval
            'google_id' => $googleData['google_id'],
            'avatar' => $googleData['avatar'],
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(32)),
        ]);

        if ($isFarmer) {
            Farmer::create([
                'user_id' => $user->id,
                'stall_name' => $validated['stall_name'] ?: ($googleData['name'] . "'s Farm Stall"),
                'contact_person' => $googleData['name'],
                'contact_number' => $validated['contact_number'],
                'address' => $validated['address'],
                'cutoff_hours' => 2,
            ]);
        }

        try {
            Mail::to($user->email)->send(new WelcomeUserMail($user));
        } catch (\Throwable $e) {
            Log::info('Welcome email notification skipped or deferred: ' . $e->getMessage());
        }

        session()->forget('google_auth_data');
        session()->forget('google_intended_role');

        Auth::login($user, true);
        $request->session()->regenerate();

        if ($isFarmer) {
            return redirect()->route('farmer.dashboard')
                ->with('warning', 'Welcome to MarketLink! Your farmer account has been created with Google. It is currently pending admin review before stall listings go live.');
        }

        return redirect()->route('customer.dashboard')
            ->with('success', 'Welcome to MarketLink! Your Google-verified customer account is ready.');
    }
}
