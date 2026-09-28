<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $favorites = Favorite::where('customer_id', $user->id)->get();

        $favoriteProductIds = $favorites->where('item_type', 'product')->pluck('item_id');
        $favoriteFarmerIds = $favorites->where('item_type', 'farmer')->pluck('item_id');
        $favoriteMarketIds = $favorites->where('item_type', 'market')->pluck('item_id');

        $products = Product::whereIn('id', $favoriteProductIds)->with('farmer.market')->get();
        $farmers = Farmer::whereIn('id', $favoriteFarmerIds)->with('market')->get();
        $markets = Market::whereIn('id', $favoriteMarketIds)->get();

        return view('customer.favorites', compact('products', 'farmers', 'markets'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:farmer,product,market',
            'item_id' => 'required|integer',
        ]);

        $userId = Auth::id();
        $type = $request->input('item_type');
        $itemId = (int) $request->input('item_id');

        $exists = match ($type) {
            'farmer' => Farmer::where('id', $itemId)->exists(),
            'product' => Product::where('id', $itemId)->exists(),
            'market' => Market::where('id', $itemId)->exists(),
            default => false,
        };

        if (!$exists) {
            return $request->wantsJson()
                ? response()->json(['error' => 'Item not found.'], 404)
                : back()->with('error', 'Item not found.');
        }

        $fav = Favorite::where('customer_id', $userId)
            ->where('item_type', $type)
            ->where('item_id', $itemId)
            ->first();

        if ($fav) {
            $fav->delete();
            $status = 'removed';
            $message = 'Removed from favorites.';
        } else {
            Favorite::create([
                'customer_id' => $userId,
                'item_type' => $type,
                'item_id' => $itemId,
            ]);
            $status = 'added';
            $message = 'Saved to your favorites!';
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
