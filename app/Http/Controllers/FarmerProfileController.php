<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateFarmerProfileRequest;
use App\Models\FarmerProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FarmerProfileController extends Controller
{
    /**
     * Get authenticated farmer profile
     */
    public function show(Request $request): JsonResponse
    {
        $farmer = $request->user()->load([
            'farmerProfile',
        ]);

        return response()->json([
            'farmer' => $farmer,
        ], 200);
    }

    /**
     * Update farmer profile and stall information
     */
    public function update(
        UpdateFarmerProfileRequest $request
    ): JsonResponse {
        $user = $request->user();

        /*
         * Update basic user information.
         */
        $user->update([
            'name' => $request->input(
                'name',
                $user->name
            ),
            'phone' => $request->input(
                'phone',
                $user->phone
            ),
            'address' => $request->input(
                'address',
                $user->address
            ),
        ]);

        /*
         * Create or update farmer profile.
         */
        FarmerProfile::updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'stall_name' => $request->input('stall_name'),
                'contact_person' => $request->input('contact_person'),
                'address' => $request->input('profile_address'),
                'operating_days' => $request->input('operating_days'),
                'pickup_time' => $request->input('pickup_time'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'description' => $request->input('description'),
            ]
        );

        return response()->json([
            'message' => 'Farmer profile updated successfully.',
            'farmer' => $user->fresh()->load([
                'farmerProfile',
            ]),
        ], 200);
    }
}