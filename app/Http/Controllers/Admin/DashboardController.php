<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'total_farmers' => Farmer::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_markets' => Market::count(),
            'total_orders' => Order::count(),
            'total_volume' => Order::sum('total_amount'),
        ];

        $pendingFarmers = Farmer::where(function ($q) {
            $q->where('is_approved', false)
              ->orWhere('approval_status', 'pending')
              ->orWhereHas('user', fn($uq) => $uq->where('is_approved', false));
        })->with('user', 'market')->get();

        $activeFarmers = Farmer::where('is_approved', true)
            ->where(function ($q) {
                $q->where('approval_status', 'approved')->orWhereNull('approval_status');
            })
            ->whereHas('user', function ($uq) {
                $uq->where('is_approved', true)->where('is_active', true);
            })->with('user', 'market')->get();

        $customers = User::where('role', 'customer')->latest()->take(10)->get();
        $recentOrders = Order::with('customer', 'farmer')->latest()->take(8)->get();

        // Chart 1: Revenue & Order Counts per Market (SRS §1.6: "revenue generated per market")
        $markets = Market::all();
        $completedRevenuesByMarket = Order::where('order_status', 'completed')
            ->selectRaw('market_id, SUM(total_amount) as total_rev')
            ->groupBy('market_id')
            ->pluck('total_rev', 'market_id');

        $orderCountsByMarket = Order::selectRaw('market_id, COUNT(*) as total_orders')
            ->groupBy('market_id')
            ->pluck('total_orders', 'market_id');

        $marketRevenueLabels = [];
        $marketRevenueData = [];
        $marketOrderCountData = [];

        foreach ($markets as $m) {
            $marketRevenueLabels[] = $m->name;
            $marketRevenueData[] = (float) ($completedRevenuesByMarket[$m->id] ?? 0);
            $marketOrderCountData[] = (int) ($orderCountsByMarket[$m->id] ?? 0);
        }

        // Chart 2: Order Pipeline Status Breakdown (SRS §1.5)
        $ordersByStatus = Order::selectRaw('order_status, COUNT(*) as cnt')
            ->groupBy('order_status')
            ->pluck('cnt', 'order_status');

        $statuses = ['placed', 'accepted', 'ready_for_pickup', 'completed', 'cancelled'];
        $orderStatusLabels = ['Placed', 'Accepted', 'Ready for Pickup', 'Completed', 'Cancelled'];
        $orderStatusData = [];
        foreach ($statuses as $st) {
            $orderStatusData[] = (int) ($ordersByStatus[$st] ?? 0);
        }

        // Chart 3: Popular Categories (SRS §1.6: "popular categories")
        $categories = Category::withCount('products')->get();
        $categoryLabels = $categories->pluck('name')->toArray();
        $categoryCounts = $categories->pluck('products_count')->toArray();

        // 4. Platform System Settings & Quick Controls
        $platformSettings = \App\Models\Setting::pluck('value', 'key')->toArray();

        // 5. Homepage Spotlight & Recent Reviews
        $featuredSale = \App\Models\Sale::with(['farmer.market', 'products'])->where('is_featured', true)->first();
        $activeSalesCount = \App\Models\Sale::active()->count();
        $recentReviews = \App\Models\Review::with(['customer', 'product', 'farmer'])->latest()->take(3)->get();

        return view('admin.dashboard', compact(
            'metrics',
            'pendingFarmers',
            'activeFarmers',
            'customers',
            'recentOrders',
            'marketRevenueLabels',
            'marketRevenueData',
            'marketOrderCountData',
            'orderStatusLabels',
            'orderStatusData',
            'categoryLabels',
            'categoryCounts',
            'platformSettings',
            'featuredSale',
            'activeSalesCount',
            'recentReviews'
        ));
    }

    public function approveFarmer($id)
    {
        $farmer = Farmer::with('user')->findOrFail($id);
        $farmer->is_approved = true;
        $farmer->approval_status = 'approved';
        $farmer->rejection_reason = null;
        $farmer->save();

        if ($farmer->user) {
            $farmer->user->is_approved = true;
            $farmer->user->is_active = true;
            $farmer->user->status = 'active';
            $farmer->user->save();
        }

        return back()->with('success', "Stall '{$farmer->stall_name}' has been approved and can now list products.");
    }

    public function suspendFarmer($id)
    {
        $farmer = Farmer::with('user')->findOrFail($id);
        $farmer->is_approved = false;
        $farmer->approval_status = 'suspended';
        $farmer->rejection_reason = 'Suspended by platform administrator.';
        $farmer->save();

        if ($farmer->user) {
            $farmer->user->is_approved = false;
            $farmer->user->is_active = false;
            $farmer->user->status = 'suspended';
            $farmer->user->save();
        }

        return back()->with('warning', "Stall '{$farmer->stall_name}' has been suspended.");
    }

    public function toggleCustomerStatus($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);
        $customer->is_active = !$customer->is_active;
        $customer->status = $customer->is_active ? 'active' : 'suspended';
        $customer->save();

        $status = $customer->is_active ? 'activated' : 'deactivated';
        return back()->with('info', "Customer '{$customer->name}' has been {$status}.");
    }
}
