<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFarmerApproved
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $profile = $user?->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found. Please complete your registration.',
            ], 404);
        }

        if ($profile->approval_status === 'pending') {
            return response()->json([
                'status' => 'error',
                'approval_status' => 'pending',
                'message' => 'Your farmer account is currently pending administrator approval.',
            ], 403);
        }

        if ($profile->approval_status === 'rejected') {
            return response()->json([
                'status' => 'error',
                'approval_status' => 'rejected',
                'rejection_reason' => $profile->rejection_reason,
                'message' => 'Your farmer account application has been rejected.',
            ], 403);
        }

        if ($profile->approval_status === 'suspended') {
            return response()->json([
                'status' => 'error',
                'approval_status' => 'suspended',
                'rejection_reason' => $profile->rejection_reason,
                'message' => 'Your farmer account has been suspended by administration.',
            ], 403);
        }

        return $next($request);
    }
}
