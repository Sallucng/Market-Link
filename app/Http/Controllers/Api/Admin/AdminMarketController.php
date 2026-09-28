<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminMarketRequest;
use App\Models\Market;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminMarketController extends Controller
{
    /**
     * List all physical farmer markets.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Market::withCount('farmers');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $markets = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data'   => $markets,
        ]);
    }

    /**
     * Create a new physical farmer market.
     */
    public function store(AdminMarketRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('markets', 'public');
        }

        $market = Market::create([
            'name'           => $validated['name'],
            'location'       => $validated['location'] ?? $request->input('city', $validated['address']),
            'address'        => $validated['address'],
            'latitude'       => $validated['latitude'] ?? null,
            'longitude'      => $validated['longitude'] ?? null,
            'operating_days' => $validated['operating_days'],
            'open_time'      => $validated['open_time'] ?? $validated['opening_time'] ?? '08:00',
            'close_time'     => $validated['close_time'] ?? $validated['closing_time'] ?? '18:00',
            'status'         => $validated['status'] ?? 'active',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Market created successfully.',
            'data'    => $market,
        ], 201);
    }

    /**
     * Show single physical farmer market.
     */
    public function show($id): JsonResponse
    {
        $market = Market::with(['farmers.user'])->find($id);

        if (!$market) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Market not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $market,
        ]);
    }

    /**
     * Update an existing physical farmer market.
     */
    public function update($id, AdminMarketRequest $request): JsonResponse
    {
        $market = Market::find($id);

        if (!$market) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Market not found.',
            ], 404);
        }

        $validated = $request->validated();
        $updateData = collect($validated)->except(['image'])->toArray();

        if ($request->hasFile('image')) {
            if ($market->image && Storage::disk('public')->exists($market->image)) {
                Storage::disk('public')->delete($market->image);
            }
            $updateData['image'] = $request->file('image')->store('markets', 'public');
        }

        $market->update($updateData);

        return response()->json([
            'status'  => 'success',
            'message' => 'Market updated successfully.',
            'data'    => $market,
        ]);
    }

    /**
     * Delete a physical farmer market.
     */
    public function destroy($id): JsonResponse
    {
        $market = Market::find($id);

        if (!$market) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Market not found.',
            ], 404);
        }

        if ($market->image && Storage::disk('public')->exists($market->image)) {
            Storage::disk('public')->delete($market->image);
        }

        $market->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Market deleted successfully.',
        ]);
    }
}
