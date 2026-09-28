<?php

namespace App\Http\Controllers\Api\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\FarmerProfileUpdateRequest;
use App\Models\Market;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmerProfileController extends Controller
{
    /**
     * Display the authenticated farmer's profile with assigned markets.
     */
    public function show(Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile()->with('markets')->first();

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found for the authenticated user.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $profile,
        ]);
    }

    /**
     * Update the authenticated farmer's business profile, coordinates, operating days, and pickup time slots.
     */
    public function update(FarmerProfileUpdateRequest $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $validated = $request->validated();

        DB::transaction(function () use ($profile, $validated) {
            $profile->update([
                'business_name' => $validated['business_name'] ?? $profile->business_name,
                'description' => array_key_exists('description', $validated) ? $validated['description'] : $profile->description,
                'stall_number' => array_key_exists('stall_number', $validated) ? $validated['stall_number'] : $profile->stall_number,
                'address' => $validated['address'] ?? $profile->address,
                'latitude' => array_key_exists('latitude', $validated) ? $validated['latitude'] : $profile->latitude,
                'longitude' => array_key_exists('longitude', $validated) ? $validated['longitude'] : $profile->longitude,
                'operating_days' => $validated['operating_days'] ?? $profile->operating_days,
                'pickup_start_time' => array_key_exists('pickup_start_time', $validated) ? $validated['pickup_start_time'] : $profile->pickup_start_time,
                'pickup_end_time' => array_key_exists('pickup_end_time', $validated) ? $validated['pickup_end_time'] : $profile->pickup_end_time,
            ]);

            // Sync assigned physical market associations if provided
            if (isset($validated['market_ids'])) {
                $profile->markets()->sync($validated['market_ids']);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Farmer profile updated successfully.',
            'data' => $profile->fresh('markets'),
        ]);
    }

    /**
     * List all physical markets with current farmer association status.
     */
    public function markets(Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        $markets = Market::where('status', 'active')
            ->get()
            ->map(function ($market) use ($profile) {
                $isAssociated = $profile ? $profile->markets->contains('id', $market->id) : false;
                return [
                    'id' => $market->id,
                    'name' => $market->name,
                    'location' => $market->location,
                    'address' => $market->address,
                    'latitude' => $market->latitude,
                    'longitude' => $market->longitude,
                    'operating_days' => $market->operating_days,
                    'open_time' => $market->open_time,
                    'close_time' => $market->close_time,
                    'is_associated' => $isAssociated,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $markets,
        ]);
    }

    /**
     * Associate farmer with a market.
     */
    public function joinMarket(Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        $request->validate([
            'market_id' => ['required', 'integer', 'exists:markets,id'],
            'assigned_stall' => ['nullable', 'string', 'max:50'],
        ]);

        $marketId = $request->input('market_id');
        $stall = $request->input('assigned_stall', $profile->stall_number);

        if ($profile->markets()->where('market_id', $marketId)->exists()) {
            $profile->markets()->updateExistingPivot($marketId, [
                'assigned_stall' => $stall,
                'status' => 'pending',
            ]);
        } else {
            $profile->markets()->attach($marketId, [
                'assigned_stall' => $stall,
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Market association request submitted successfully.',
            'data' => $profile->fresh('markets'),
        ]);
    }

    /**
     * Disassociate farmer from a market.
     */
    public function leaveMarket($marketId, Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile->markets()->where('market_id', $marketId)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not currently associated with this market.',
            ], 404);
        }

        $profile->markets()->detach($marketId);

        return response()->json([
            'status' => 'success',
            'message' => 'Market association removed successfully.',
            'data' => $profile->fresh('markets'),
        ]);
    }
}
