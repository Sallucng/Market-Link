<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // Get customer's favorites
    public function index(Request $request)
    {
        $favorites = Favorite::where('customer_id', $request->user()->id)
            ->with(['farmer', 'product', 'market'])
            ->get();

        return response()->json([
            'message' => 'Favorites retrieved successfully.',
            'favorites' => $favorites,
        ]);
    }

    // Add a favorite
    public function store(Request $request)
    {
        $request->validate([
            'farmer_id' => 'nullable|exists:users,id',
            'product_id' => 'nullable|exists:products,id',
            'market_id' => 'nullable|exists:markets,id',
        ]);

        if (
            !$request->farmer_id &&
            !$request->product_id &&
            !$request->market_id
        ) {
            return response()->json([
                'message' => 'Please provide farmer_id, product_id, or market_id.'
            ], 422);
        }

        $favorite = Favorite::create([
            'customer_id' => $request->user()->id,
            'farmer_id' => $request->farmer_id,
            'product_id' => $request->product_id,
            'market_id' => $request->market_id,
        ]);

        return response()->json([
            'message' => 'Favorite added successfully.',
            'favorite' => $favorite,
        ], 201);
    }

    // Remove a favorite
    public function destroy($id, Request $request)
    {
        $favorite = Favorite::where('id', $id)
            ->where('customer_id', $request->user()->id)
            ->first();

        if (!$favorite) {
            return response()->json([
                'message' => 'Favorite not found.'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'message' => 'Favorite removed successfully.'
        ]);
    }
}