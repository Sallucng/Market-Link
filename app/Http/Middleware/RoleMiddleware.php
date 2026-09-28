<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // For API requests, delegate to dedicated JSON API guards
        if ($request->is('api/*') || $request->expectsJson()) {
            if (in_array('admin', $roles)) {
                return app(\App\Http\Middleware\EnsureUserIsAdmin::class)->handle($request, $next);
            }
            if (in_array('farmer', $roles)) {
                return app(\App\Http\Middleware\EnsureUserIsFarmer::class)->handle($request, $next);
            }
            $user = $request->user();
            if (!$user || !in_array($user->role, $roles)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access.',
                ], 403);
            }
            if ($user->status === 'suspended' || !$user->is_active) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Account is suspended or deactivated.',
                ], 403);
            }
            return $next($request);
        }

        // For web session requests
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this area.');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact administration.');
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized access: You do not have permission to view this portal.');
        }

        return $next($request);
    }
}
