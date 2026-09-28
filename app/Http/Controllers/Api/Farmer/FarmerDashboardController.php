<?php

namespace App\Http\Controllers\Api\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmerDashboardController extends Controller
{
    /**
     * Aggregate farmer sales, orders metrics, and top-selling products.
     */
    public function statistics(Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $farmerId = $request->user()->id;

        // High performance single-query aggregation across all order states and revenue
        $stats = Order::where('farmer_id', $farmerId)
            ->selectRaw("
                COUNT(*) as total_orders,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
                COUNT(CASE WHEN status = 'accepted' THEN 1 END) as accepted_orders,
                COUNT(CASE WHEN status = 'ready_for_pickup' THEN 1 END) as ready_orders,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders,
                COUNT(CASE WHEN status = 'declined' THEN 1 END) as declined_orders,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders,
                COALESCE(SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END), 0) as gross_revenue
            ")
            ->first();

        $bestSellingProducts = OrderItem::whereHas('order', function ($query) use ($farmerId) {
                $query->where('farmer_id', $farmerId)
                      ->where('status', 'completed');
            })
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity_sold'),
                DB::raw('SUM(order_items.subtotal) as total_sales')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity_sold')
            ->limit(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_orders' => (int)($stats->total_orders ?? 0),
                'pending_orders' => (int)($stats->pending_orders ?? 0),
                'accepted_orders' => (int)($stats->accepted_orders ?? 0),
                'ready_orders' => (int)($stats->ready_orders ?? 0),
                'completed_orders' => (int)($stats->completed_orders ?? 0),
                'declined_orders' => (int)($stats->declined_orders ?? 0),
                'cancelled_orders' => (int)($stats->cancelled_orders ?? 0),
                'revenue_summary' => [
                    'completed_revenue' => (float)($stats->gross_revenue ?? 0),
                    'currency' => 'USD',
                ],
                'best_selling_products' => $bestSellingProducts,
            ],
        ]);
    }
}
