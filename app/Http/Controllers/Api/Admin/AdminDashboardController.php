<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Aggregate platform-wide statistics for the admin dashboard.
     *
     * Metrics:
     * - Total Farmers
     * - Total Customers
     * - Total Active Markets
     * - Total System Orders
     * - Total Platform Revenue
     */
    public function stats(Request $request): JsonResponse
    {
        $userStats = User::selectRaw("
            COUNT(CASE WHEN role = 'farmer' THEN 1 END) as total_farmers,
            COUNT(CASE WHEN role = 'farmer' AND status = 'suspended' THEN 1 END) as suspended_farmers,
            COUNT(CASE WHEN role = 'customer' THEN 1 END) as total_customers,
            COUNT(CASE WHEN role = 'customer' AND status = 'active' THEN 1 END) as active_customers,
            COUNT(CASE WHEN role = 'customer' AND status != 'active' THEN 1 END) as inactive_customers
        ")->first();

        $farmerStats = FarmerProfile::selectRaw("
            COUNT(CASE WHEN is_approved = 1 THEN 1 END) as approved_farmers,
            COUNT(CASE WHEN is_approved = 0 AND rejection_reason IS NULL THEN 1 END) as pending_farmers
        ")->first();

        $marketStats = Market::selectRaw("
            COUNT(*) as total_markets,
            COUNT(CASE WHEN status = 'active' THEN 1 END) as active_markets
        ")->first();

        $orderStats = Order::selectRaw("
            COUNT(*) as total_orders,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders,
            COUNT(CASE WHEN status IN ('declined', 'cancelled') THEN 1 END) as cancelled_orders,
            COALESCE(SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END), 0) as total_revenue
        ")->first();

        $productStats = Product::selectRaw("
            COUNT(*) as total_products,
            COUNT(CASE WHEN is_moderated = 1 THEN 1 END) as flagged_products
        ")->first();

        $reviewStats = Review::selectRaw("
            COUNT(*) as total_reviews,
            COUNT(CASE WHEN is_moderated = 1 THEN 1 END) as flagged_reviews
        ")->first();

        $totalFarmers = (int)($userStats->total_farmers ?? 0);
        $approvedFarmers = (int)($farmerStats->approved_farmers ?? 0);
        $pendingFarmers = (int)($farmerStats->pending_farmers ?? 0);
        $suspendedFarmers = (int)($userStats->suspended_farmers ?? 0);

        $totalCustomers = (int)($userStats->total_customers ?? 0);
        $activeCustomers = (int)($userStats->active_customers ?? 0);
        $inactiveCustomers = (int)($userStats->inactive_customers ?? 0);

        $totalMarkets = (int)($marketStats->total_markets ?? 0);
        $activeMarkets = (int)($marketStats->active_markets ?? 0);

        $totalOrders = (int)($orderStats->total_orders ?? 0);
        $pendingOrders = (int)($orderStats->pending_orders ?? 0);
        $completedOrders = (int)($orderStats->completed_orders ?? 0);
        $cancelledOrders = (int)($orderStats->cancelled_orders ?? 0);
        $totalRevenue = (float)($orderStats->total_revenue ?? 0);

        $totalProducts = (int)($productStats->total_products ?? 0);
        $flaggedProducts = (int)($productStats->flagged_products ?? 0);

        $totalReviews = (int)($reviewStats->total_reviews ?? 0);
        $flaggedReviews = (int)($reviewStats->flagged_reviews ?? 0);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_farmers' => $totalFarmers,
                'total_customers' => $totalCustomers,
                'total_active_markets' => $activeMarkets,
                'total_system_orders' => $totalOrders,
                'total_platform_revenue' => $totalRevenue,
                'farmers' => [
                    'total' => $totalFarmers,
                    'approved' => $approvedFarmers,
                    'pending_approval' => $pendingFarmers,
                    'suspended' => $suspendedFarmers,
                ],
                'customers' => [
                    'total' => $totalCustomers,
                    'active' => $activeCustomers,
                    'inactive' => $inactiveCustomers,
                ],
                'markets' => [
                    'total' => $totalMarkets,
                    'active' => $activeMarkets,
                ],
                'orders' => [
                    'total' => $totalOrders,
                    'pending' => $pendingOrders,
                    'completed' => $completedOrders,
                    'cancelled' => $cancelledOrders,
                    'total_revenue' => $totalRevenue,
                ],
                'catalog' => [
                    'total_products' => $totalProducts,
                    'flagged_products' => $flaggedProducts,
                    'total_reviews' => $totalReviews,
                    'flagged_reviews' => $flaggedReviews,
                ],
            ],
        ]);
    }

    /**
     * Alias for stats endpoint.
     */
    public function index(Request $request): JsonResponse
    {
        return $this->stats($request);
    }
}
