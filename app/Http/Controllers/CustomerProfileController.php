<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCustomerProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerProfileController extends Controller
{
    /**
     * Get authenticated customer profile
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => $user,
        ], 200);
    }

    /**
     * Update authenticated customer profile
     */
    public function update(
        UpdateCustomerProfileRequest $request
    ): JsonResponse {
        $user = $request->user();

        $validated = $request->validated();

        // Customer can only update allowed profile fields.
        // Role, status and password are not changed here.
        $allowedFields = [
            'name',
            'phone',
            'address',
        ];

        $user->update(
            array_intersect_key(
                $validated,
                array_flip($allowedFields)
            )
        );

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh(),
        ], 200);
    }
}