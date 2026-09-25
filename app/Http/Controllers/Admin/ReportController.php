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

    public function export()
    {
        $fileName = 'marketlink_platform_report_' . date('Y-m-d_His') . '.csv';
        $orders = Order::with(['farmer.market', 'customer', 'items.product'])->latest()->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Order Number', 'Date', 'Customer Name', 'Customer Email', 'Market', 'Farmer Stall', 'Total Amount ($)', 'Payment Method', 'Status', 'Pickup Date', 'Pickup Window', 'Items Summary'];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                $itemsSummary = $order->items->map(function ($i) {
                    return ($i->product->name ?? 'Product') . ' (x' . $i->quantity . ')';
                })->implode('; ');

                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->customer->name ?? 'N/A',
                    $order->customer->email ?? 'N/A',
                    $order->farmer->market->name ?? 'N/A',
                    $order->farmer->stall_name ?? 'N/A',
                    number_format($order->total_amount, 2),
                    $order->payment_method,
                    $order->order_status,
                    $order->pickup_date ? $order->pickup_date->format('Y-m-d') : 'N/A',
                    $order->pickup_time_slot ?? 'N/A',
                    $itemsSummary,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
