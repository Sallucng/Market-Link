<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $completedOrders = Order::where('order_status', 'completed')->count();
        $totalRevenue = Order::where('order_status', 'completed')->sum('total_amount');

        // Market-wise performance & revenue
        $marketStats = Market::withCount(['farmers', 'orders'])
            ->get()
            ->map(function ($market) {
                $revenue = Order::where('market_id', $market->id)
                    ->where('order_status', 'completed')
                    ->sum('total_amount');
                $market->revenue = $revenue;
                return $market;
            });

        // Most active farmers (by pre-order volume)
        $topFarmers = Farmer::withCount('orders')
            ->with('market', 'user')
            ->orderBy('orders_count', 'desc')
            ->take(8)
            ->get()
            ->map(function ($farmer) {
                $farmer->total_sales = Order::where('farmer_id', $farmer->id)
                    ->where('order_status', 'completed')
                    ->sum('total_amount');
                return $farmer;
            });

        // Status breakdown
        $statusBreakdown = Order::select('order_status', DB::raw('count(*) as count'))
            ->groupBy('order_status')
            ->pluck('count', 'order_status')
            ->toArray();

        // Chart data arrays for reports
        $marketNames = $marketStats->pluck('name')->toArray();
        $marketRevenues = $marketStats->pluck('revenue')->toArray();
        $marketOrderCounts = $marketStats->pluck('orders_count')->toArray();

        // Popular Categories distribution (SRS §1.6)
        $categories = Category::withCount('products')->get();
        $categoryNames = $categories->pluck('name')->toArray();
        $categoryProductCounts = $categories->pluck('products_count')->toArray();

        return view('admin.reports.index', compact(
            'totalOrders',
            'completedOrders',
            'totalRevenue',
            'marketStats',
            'topFarmers',
            'statusBreakdown',
            'marketNames',
            'marketRevenues',
            'marketOrderCounts',
            'categoryNames',
            'categoryProductCounts'
        ));
    }
}
