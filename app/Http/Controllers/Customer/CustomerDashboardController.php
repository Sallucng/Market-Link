<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
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

        // Customer Summary Stats
        $totalOrdersCount = Order::where('customer_id', $user->id)->count();
        $completedOrdersCount = Order::where('customer_id', $user->id)->where('order_status', 'completed')->count();
        $activeOrdersCount = $activeOrders->count();
        $savedFavoritesCount = $favorites->count();

        return view('customer.dashboard', compact(
            'user',
            'activeOrders',
            'recentOrders',
            'favoriteProducts',
            'favoriteFarmers',
            'preferredMarkets',
            'notifications',
            'totalOrdersCount',
            'completedOrdersCount',
            'activeOrdersCount',
            'savedFavoritesCount'
        ));
    }

    /**
     * Show Customer Settings & Account Preferences Hub.
     */
    public function settings()
    {
        $user = Auth::user();
        $preferences = $user->preferences ?? [];
        $markets = Market::orderBy('name')->get();

        return view('customer.settings', compact('user', 'preferences', 'markets'));
    }

    /**
     * Update Customer Account Preferences.
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'contact_number' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'preferred_market_id' => 'nullable|exists:markets,id',
            'pickup_reminder_timing' => 'nullable|string|in:1h,2h,morning,none',
            'default_pickup_window_preference' => 'nullable|string|max:50',
            'allergy_notes' => 'nullable|string|max:300',
            'dietary_preferences' => 'nullable|array',
        ]);

        $user->name = $validated['name'];
        $user->contact_number = $validated['contact_number'];
        $user->address = $validated['address'];

        $prefs = $user->preferences ?? [];
        $prefs['preferred_market_id'] = $validated['preferred_market_id'] ?? null;
        $prefs['pickup_reminder_timing'] = $validated['pickup_reminder_timing'] ?? '2h';
        $prefs['default_pickup_window_preference'] = $validated['default_pickup_window_preference'] ?? 'early_morning';
        $prefs['allergy_notes'] = $validated['allergy_notes'] ?? '';
        $prefs['dietary_preferences'] = $request->input('dietary_preferences', []);

        // Notification toggles
        $toggles = [
            'notify_email_order_accepted',
            'notify_email_ready_pickup',
            'notify_sms_ready',
            'notify_farm_updates',
            'notify_restock_favorites',
            'display_review_anonymously',
            'auto_save_orders',
        ];

        foreach ($toggles as $tog) {
            $prefs[$tog] = $request->has($tog);
        }

        $user->preferences = $prefs;
        $user->save();

        return redirect()->route('customer.settings.index')->with('success', 'Your account preferences and notification settings have been updated!');
    }

    /**
     * AJAX quick toggle for dashboard switches.
     */
    public function quickToggle(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required',
        ]);

        $user = Auth::user();
        $key = $request->input('key');
        $value = $request->input('value');

        $prefs = $user->preferences ?? [];

        if ($key === 'dietary_toggle') {
            $tag = $request->input('tag');
            $dietary = $prefs['dietary_preferences'] ?? [];
            if (in_array($tag, $dietary)) {
                $dietary = array_values(array_diff($dietary, [$tag]));
            } else {
                $dietary[] = $tag;
            }
            $prefs['dietary_preferences'] = $dietary;
        } elseif ($key === 'preferred_market_id' || $key === 'pickup_reminder_timing') {
            $prefs[$key] = $value;
        } else {
            $prefs[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        $user->preferences = $prefs;
        $user->save();

        return response()->json([
            'success' => true,
            'key' => $key,
            'value' => $value,
            'preferences' => $prefs,
            'message' => 'Preference updated.',
        ]);
    }
}
