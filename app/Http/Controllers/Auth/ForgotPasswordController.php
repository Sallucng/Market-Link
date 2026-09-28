<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if (!$user->is_active) {
                return back()->withInput()->with('error', 'This account has been deactivated. Please contact platform administrators.');
            }

            try {
                $token = Password::broker()->createToken($user);
                $resetUrl = route('password.reset', [
                    'token' => $token,
                    'email' => $user->email,
                ]);

                Mail::to($user->email)->send(new ResetPasswordMail($user, $resetUrl));
            } catch (\Throwable $e) {
                Log::error('Password reset email dispatch failed: ' . $e->getMessage());
                return back()->withInput()->with('error', 'Unable to send password reset email at this moment. Please try again shortly.');
            }
        }

        // Return generic success message to prevent user enumeration attacks
        return back()->with('status', 'If an account exists with that email address, we have sent a secure password reset link.');
    }

    /**
     * Display the password reset form for the given token.
     */
    public function showResetForm(Request $request, string $token)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', old('email', '')),
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Your password has been successfully reset! Please sign in with your new credentials.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => [trans($status)]]);
    }
}
