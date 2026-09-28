<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FarmerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isFarmer()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Farmer access required.',
            ], 403);
        }

        if ($user->status === 'suspended') {
            return response()->json([
                'status' => 'error',
                'message' => 'Your farmer account has been suspended by an administrator.',
            ], 403);
        }

        if (!$user->isActive() && $user->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Your farmer account is not active.',
            ], 403);
        }

        return $next($request);
    }
}
