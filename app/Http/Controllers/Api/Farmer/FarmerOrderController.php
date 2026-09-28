<?php

namespace App\Http\Controllers\Api\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\OrderStatusUpdateRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmerOrderController extends Controller
{
    /**
     * List incoming customer orders for the authenticated farmer.
     */
    public function index(Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $query = Order::where('farmer_profile_id', $profile->id)
            ->with(['customer:id,name,email,phone', 'market:id,name,address', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to incoming active orders
            $query->whereIn('status', ['pending', 'accepted', 'ready_for_pickup']);
        }

        if ($request->filled('pickup_slot')) {
            $query->where('pickup_slot', 'like', "%{$request->pickup_slot}%");
        }

        $orders = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }

    /**
     * View detailed order information.
     */
    public function show($id, Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $order = Order::where('farmer_profile_id', $profile->id)
            ->with(['customer:id,name,email,phone', 'market', 'items.product', 'review'])
            ->find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $order,
        ]);
    }

    /**
     * Update order status with validation, cutoff check, and DB transaction on decline.
     */
    public function updateStatus($id, OrderStatusUpdateRequest $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $newStatus = $request->validated('status');
        $declineReason = $request->validated('decline_reason');

        return DB::transaction(function () use ($profile, $id, $newStatus, $declineReason) {
            $order = Order::where('farmer_profile_id', $profile->id)
                ->with('items')
                ->lockForUpdate()
                ->find($id);

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found.',
                ], 404);
            }

            // Cutoff time enforcement (timezone-safe)
            if ($order->isPastCutoff()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The cutoff time for this order has passed. Status cannot be modified.',
                ], 422);
            }

            // State transition guards under lock
            if ($newStatus === 'accepted' && $order->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => "Order cannot be accepted because current status is '{$order->status}'.",
                ], 422);
            }

            if ($newStatus === 'ready_for_pickup' && $order->status !== 'accepted') {
                return response()->json([
                    'status' => 'error',
                    'message' => "Order must be in 'accepted' status before marking ready for pickup.",
                ], 422);
            }

            if ($newStatus === 'declined' && in_array($order->status, ['declined', 'completed', 'cancelled'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Order cannot be declined because current status is '{$order->status}'.",
                ], 422);
            }

            $order->status = $newStatus;
            if ($newStatus === 'declined') {
                $order->decline_reason = $declineReason;
                // Restock items into product inventory atomically under lock
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
                    }
                }
            }
            $order->save();

            return response()->json([
                'status' => 'success',
                'message' => "Order status updated to '{$newStatus}' successfully.",
                'data' => $order->fresh(['customer:id,name,email,phone', 'items.product']),
            ]);
        });
    }

    /**
     * Accept an incoming customer order.
     */
    public function accept($id, Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        return DB::transaction(function () use ($profile, $id) {
            $order = Order::where('farmer_profile_id', $profile->id)->lockForUpdate()->find($id);

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found.',
                ], 404);
            }

            if ($order->isPastCutoff()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The cutoff time for this order has passed. Status cannot be modified.',
                ], 422);
            }

            if ($order->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => "Order cannot be accepted because current status is '{$order->status}'.",
                ], 422);
            }

            $order->status = 'accepted';
            $order->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Order accepted successfully.',
                'data' => $order->fresh(['customer:id,name,email,phone', 'items']),
            ]);
        });
    }

    /**
     * Decline an order with mandatory reason and restock products.
     */
    public function decline($id, Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $request->validate([
            'decline_reason' => ['required', 'string', 'max:500'],
        ]);

        $declineReason = $request->input('decline_reason');

        return DB::transaction(function () use ($profile, $id, $declineReason) {
            $order = Order::where('farmer_profile_id', $profile->id)->with('items')->lockForUpdate()->find($id);

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found.',
                ], 404);
            }

            if (in_array($order->status, ['declined', 'completed', 'cancelled'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Order cannot be declined because current status is '{$order->status}'.",
                ], 422);
            }

            $order->status = 'declined';
            $order->decline_reason = $declineReason;
            $order->save();

            // Return stock back to inventory atomically
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order has been declined and items restocked.',
                'data' => $order->fresh(),
            ]);
        });
    }

    /**
     * Mark order as ready for pickup.
     */
    public function markReadyForPickup($id, Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        return DB::transaction(function () use ($profile, $id) {
            $order = Order::where('farmer_profile_id', $profile->id)->lockForUpdate()->find($id);

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found.',
                ], 404);
            }

            if ($order->status !== 'accepted') {
                return response()->json([
                    'status' => 'error',
                    'message' => "Order must be in 'accepted' status before marking ready for pickup.",
                ], 422);
            }

            $order->status = 'ready_for_pickup';
            $order->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Order marked as ready for pickup.',
                'data' => $order->fresh(),
            ]);
        });
    }

    /**
     * Mark order as completed after customer pickup.
     */
    public function complete($id, Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        return DB::transaction(function () use ($profile, $id) {
            $order = Order::where('farmer_profile_id', $profile->id)->lockForUpdate()->find($id);

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found.',
                ], 404);
            }

            if ($order->status !== 'ready_for_pickup') {
                return response()->json([
                    'status' => 'error',
                    'message' => "Order must be in 'ready_for_pickup' status to complete.",
                ], 422);
            }

            $order->status = 'completed';
            $order->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Order marked as completed successfully.',
                'data' => $order->fresh(),
            ]);
        });
    }

    /**
     * Order history (completed, declined, cancelled).
     */
    public function history(Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $orders = Order::where('farmer_profile_id', $profile->id)
            ->whereIn('status', ['completed', 'declined', 'cancelled'])
            ->with(['customer:id,name,email,phone', 'market:id,name', 'items.product'])
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }

    /**
     * Sales & order statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $dashboardController = new FarmerDashboardController();
        return $dashboardController->statistics($request);
    }
}
