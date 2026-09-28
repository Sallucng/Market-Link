<?php

namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Market;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    /**
     * Public list of active farmers markets.
     */
    public function markets(Request $request): JsonResponse
    {
        $query = Market::where('status', 'active');

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $markets = $query->with(['farmers' => function ($q) {
            $q->where('farmer_market.status', 'approved');
        }])->get();

        return response()->json([
            'status' => 'success',
            'data' => $markets,
        ]);
    }

    /**
     * Public list of active product categories.
     */
    public function categories(): JsonResponse
    {
        $categories = Category::where('is_active', true)->withCount('products')->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    /**
     * Active announcements (filtered by role if user is authenticated).
     */
    public function announcements(Request $request): JsonResponse
    {
        $query = Announcement::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });

        $role = $request->user()?->role ?? 'customer';

        $query->whereIn('target_audience', ['all', $role]);

        $announcements = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $announcements,
        ]);
    }
}
