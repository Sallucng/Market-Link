<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsFarmer
{
    /**
     * Handle an incoming request.
     *
     * Validates that:
     * 1. The authenticated user possesses the 'farmer' role.
     * 2. The farmer account is not suspended.
     * 3. For operations requiring approval (products, orders, reviews), the profile is active and approved.
     */
    public function handle(Request $request, Closure $next, ?string $requireApproval = null): Response
    {
        $user = $request->user();

        // 1. Verify user role
        if (!$user || $user->role !== 'farmer') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized access. Farmer account credentials required.',
            ], 403);
        }

        // 2. Verify account status
        if ($user->status === 'suspended') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Your farmer account has been suspended by an administrator.',
            ], 403);
        }

        $profile = $user->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Farmer business profile not found for this account.',
            ], 404);
        }

        // 3. Check approval status for catalog, inventory, and order operations
        $isRestrictedOperation = $requireApproval === 'approved'
            || $request->is('*products*')
            || $request->is('*orders*')
            || $request->is('*reviews*');

        if ($isRestrictedOperation) {
            if ($profile->approval_status === 'pending' || (!$profile->is_approved && empty($profile->rejection_reason))) {
                return response()->json([
                    'status'          => 'error',
                    'approval_status' => 'pending',
                    'message'         => 'Your farmer account is currently pending administrator approval.',
                ], 403);
            }

            if ($profile->approval_status === 'rejected') {
                return response()->json([
                    'status'           => 'error',
                    'approval_status'  => 'rejected',
                    'rejection_reason' => $profile->rejection_reason,
                    'message'          => 'Your farmer registration application has been rejected.',
                ], 403);
            }

            if ($profile->approval_status === 'suspended' || !$profile->is_approved) {
                return response()->json([
                    'status'           => 'error',
                    'approval_status'  => 'suspended',
                    'rejection_reason' => $profile->rejection_reason,
                    'message'          => 'Your farmer profile is currently suspended.',
                ], 403);
            }
        }

        return $next($request);
    }
}
