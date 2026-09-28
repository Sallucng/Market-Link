<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketBrowseController extends Controller
{
    /**
     * List all active markets with farmer counts
     */
    public function index(Request $request): JsonResponse
    {
        $query = Market::where('status', 'active');

        // Optional search by market name or address
        if ($request->filled('search')) {
            $search = $request->query('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $markets = $query
            ->withCount('farmers')
            ->orderBy('name')
            ->get();

        return response()->json([
            'markets' => $markets
        ], 200);
    }

    /**
     * Show detailed market profile with associated farmers
     */
    public function show($id): JsonResponse
    {
        $market = Market::where('status', 'active')
            ->with('farmers')
            ->find($id);

        if (! $market) {
            return response()->json([
                'message' => 'Market not found or inactive.'
            ], 404);
        }

        return response()->json([
            'market' => $market
        ], 200);
    }

    /**
     * View detailed profile of a specific farmer
     */
    public function showFarmer($farmerId): JsonResponse
    {
        $farmer = User::where('role', 'farmer')
            ->where('status', 'active')
            ->with([
                'farmerProfile',
                'products' => function ($query) {
                    $query->where('status', 'available')
                        ->with('category');
                }
            ])
            ->find($farmerId);

        if (! $farmer) {
            return response()->json([
                'message' => 'Farmer stall not found.'
            ], 404);
        }

        return response()->json([
            'farmer' => $farmer
        ], 200);
    }
}