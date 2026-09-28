<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FarmerOrderController extends Controller
{
    /**
     * List all orders belonging to this farmer
     */
    public function index(Request $request): JsonResponse
    {
        $farmerId = $request->user()->id;

        $orders = Order::where('farmer_id', $farmerId)
            ->with(['customer', 'items.product', 'pickupSlot'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($orders, 200);
    }

    /**
     * Get specific order details for this farmer
     */
    public function show(Request $request, $id): JsonResponse
    {
        $farmerId = $request->user()->id;

        $order = Order::where('farmer_id', $farmerId)
            ->with([
                'customer',
                'items.product',
                'pickupSlot'
            ])
            ->find($id);

        if (! $order) {
            return response()->json([
                'message' => 'Order not found for your stall.'
            ], 404);
        }

        return response()->json([
            'order' => $order
        ], 200);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $orderId): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,ready_for_pickup,completed,cancelled,declined',
        ]);

        $farmerId = $request->user()->id;

        $order = Order::where('farmer_id', $farmerId)
            ->find($orderId);

        if (! $order) {
            return response()->json([
                'message' => 'Order not found for your stall.'
            ], 404);
        }

        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order' => $order->fresh()
        ], 200);
    }
}