<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * Validates that:
     * 1. The authenticated user possesses the 'admin' role.
     * 2. The admin user account is active.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Verify admin role
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized access. Platform administrator privileges required.',
            ], 403);
        }

        // 2. Verify account is active
        if ($user->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Your administrator account has been deactivated.',
            ], 403);
        }

        return $next($request);
    }
}
