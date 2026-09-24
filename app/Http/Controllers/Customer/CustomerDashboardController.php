<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Recent orders
        $recentOrders = Order::where('customer_id', $user->id)
            ->with(['farmer.market', 'items.product'])
            ->latest()
            ->take(5)
            ->get();

        $activeOrders = Order::where('customer_id', $user->id)
            ->whereIn('order_status', ['placed', 'accepted', 'ready_for_pickup'])
            ->with(['farmer.market', 'items.product'])
            ->latest()
            ->get();

        // Favorites
        $favorites = Favorite::where('customer_id', $user->id)->get();
        $favProductIds = $favorites->where('item_type', 'product')->pluck('item_id');
        $favFarmerIds = $favorites->where('item_type', 'farmer')->pluck('item_id');
        $favMarketIds = $favorites->where('item_type', 'market')->pluck('item_id');

        $favoriteProducts = Product::whereIn('id', $favProductIds)->with('farmer.market')->take(4)->get();
        $favoriteFarmers = Farmer::whereIn('id', $favFarmerIds)->with('market')->take(4)->get();
        $preferredMarkets = Market::whereIn('id', $favMarketIds)->take(3)->get();

        // In-app notifications
        $notifications = Notification::where('user_id', $user->id)->latest()->take(5)->get();

        return view('customer.dashboard', compact(
            'user',
            'activeOrders',
            'recentOrders',
            'favoriteProducts',
            'favoriteFarmers',
            'preferredMarkets',
            'notifications'
        ));
    }
}
