<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    /**
     * Platform-wide sales and revenue report.
     */
    public function salesReport(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $totalRevenue = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('total_amount');

        $totalCompletedOrders = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        $averageOrderValue = $totalCompletedOrders > 0 ? ($totalRevenue / $totalCompletedOrders) : 0;

        $dailySales = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $topSellingCategories = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'completed')
                  ->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
            })
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', DB::raw('SUM(order_items.quantity) as items_sold'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'summary' => [
                    'total_revenue' => (float)$totalRevenue,
                    'completed_orders' => $totalCompletedOrders,
                    'average_order_value' => round((float)$averageOrderValue, 2),
                    'currency' => 'USD',
                ],
                'daily_sales' => $dailySales,
                'top_categories' => $topSellingCategories,
            ],
        ]);
    }

    /**
     * Market performance and vendor association analytics.
     */
    public function marketAnalytics(Request $request): JsonResponse
    {
        $markets = Market::withCount(['farmers' => function ($q) {
                $q->where('farmer_market.status', 'approved');
            }])
            ->get()
            ->map(function ($market) {
                $orderCount = Order::where('market_id', $market->id)->count();
                $completedRevenue = Order::where('market_id', $market->id)
                    ->where('status', 'completed')
                    ->sum('total_amount');

                return [
                    'id' => $market->id,
                    'name' => $market->name,
                    'city' => $market->city,
                    'state' => $market->state,
                    'operating_days' => $market->operating_days,
                    'approved_farmers_count' => $market->farmers_count,
                    'total_orders' => $orderCount,
                    'completed_revenue' => (float)$completedRevenue,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $markets,
        ]);
    }
}
