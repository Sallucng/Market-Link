<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PickupSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerOrderController extends Controller
{
    /**
     * Customer order history
     */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('customer_id', $request->user()->id)
            ->with([
                'farmer.farmerProfile',
                'items.product.category',
                'pickupSlot'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders, 200);
    }

    /**
     * Customer order details
     */
    public function show(Request $request, $id): JsonResponse
    {
        $order = Order::where('customer_id', $request->user()->id)
            ->with([
                'customer',
                'farmer.farmerProfile',
                'items.product.category',
                'pickupSlot'
            ])
            ->find($id);

        if (! $order) {
            return response()->json([
                'message' => 'Order not found.'
            ], 404);
        }

        return response()->json([
            'order' => $order
        ], 200);
    }

    /**
     * Checkout and place order
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $customerId = $request->user()->id;

        $cartItems = Cart::where('customer_id', $customerId)
            ->with([
                'product.farmer',
                'product.category'
            ])
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Your cart is empty.'
            ], 400);
        }

        return DB::transaction(function () use (
            $request,
            $customerId,
            $cartItems
        ) {
            $totalAmount = 0;

            /*
             * Make sure all cart products belong to
             * the same farmer because one order
             * supports one farmer.
             */
            $farmerIds = $cartItems
                ->pluck('product.farmer_id')
                ->unique();

            if ($farmerIds->count() > 1) {
                return response()->json([
                    'message' =>
                        'Please place separate orders for products from different farmers.'
                ], 400);
            }

            $farmerId = $farmerIds->first();

            /*
             * Check farmer status
             */
            $farmer = $cartItems->first()->product->farmer;

            if (! $farmer || $farmer->role !== 'farmer') {
                return response()->json([
                    'message' => 'The selected farmer is invalid.'
                ], 400);
            }

            if (! $farmer->is_active) {
                return response()->json([
                    'message' => 'This farmer is currently inactive.'
                ], 400);
            }

            /*
             * Check every product again before creating
             * the order.
             */
            foreach ($cartItems as $item) {
                $product = Product::where('id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                if (! $product) {
                    return response()->json([
                        'message' => 'One of the products no longer exists.'
                    ], 400);
                }

                if ($product->status !== 'available') {
                    return response()->json([
                        'message' =>
                            "Product {$product->name} is unavailable."
                    ], 400);
                }

                if ($product->farmer_id !== $farmerId) {
                    return response()->json([
                        'message' =>
                            'Products from different farmers cannot be placed in one order.'
                    ], 400);
                }

                if ($product->stock_quantity < $item->quantity) {
                    return response()->json([
                        'message' =>
                            "Insufficient stock for {$product->name}."
                    ], 400);
                }

                $totalAmount +=
                    $item->quantity * $product->price;
            }

            /*
             * If a pickup slot is selected,
             * make sure it belongs to the same farmer.
             */
            if ($request->filled('pickup_slot_id')) {
                $pickupSlot = PickupSlot::where(
                    'id',
                    $request->pickup_slot_id
                )
                    ->where('farmer_id', $farmerId)
                    ->where('is_active', true)
                    ->first();

                if (! $pickupSlot) {
                    return response()->json([
                        'message' =>
                            'Selected pickup slot is invalid or unavailable.'
                    ], 400);
                }
            }

            /*
             * Create order
             *
             * Database allowed statuses:
             * pending, accepted, declined,
             * ready_for_pickup, completed, cancelled
             */
            $order = Order::create([
                'customer_id' => $customerId,
                'farmer_id' => $farmerId,
                'pickup_slot_id' =>
                    $request->pickup_slot_id ?? null,
                'pickup_date' =>
                    $request->pickup_date ?? null,
                'pickup_time' =>
                    $request->pickup_time ?? null,
                'total_amount' => $totalAmount,

                'status' => 'pending',

                'notes' => $request->notes ?? null,
            ]);

            /*
             * Create order items and reduce stock
             */
            foreach ($cartItems as $item) {
                $product = Product::where(
                    'id',
                    $item->product_id
                )
                    ->lockForUpdate()
                    ->first();

                $itemTotal =
                    $item->quantity * $product->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $product->price,
                    'subtotal' => $itemTotal,
                ]);

                $product->decrement(
                    'stock_quantity',
                    $item->quantity
                );

                /*
                 * Automatically mark product as sold out
                 * when its stock reaches zero.
                 */
                if ($product->fresh()->stock_quantity <= 0) {
                    $product->update([
                        'status' => 'sold_out'
                    ]);
                }
            }

            /*
             * Clear cart after successful order
             */
            Cart::where(
                'customer_id',
                $customerId
            )->delete();

            return response()->json([
                'message' => 'Order placed successfully.',
                'order' => $order->load([
                    'customer',
                    'farmer.farmerProfile',
                    'items.product.category',
                    'pickupSlot'
                ]),
            ], 201);
        });
    }

    /**
     * Cancel customer order
     */
    public function cancel(
        Request $request,
        $id
    ): JsonResponse {
        $order = Order::where(
            'customer_id',
            $request->user()->id
        )
            ->with('items')
            ->find($id);

        if (! $order) {
            return response()->json([
                'message' => 'Order not found.'
            ], 404);
        }

        /*
         * Only pending orders can be cancelled.
         */
        if ($order->status !== 'pending') {
            return response()->json([
                'message' =>
                    'Only pending orders can be cancelled.'
            ], 400);
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);

                if ($product) {
                    $product->increment(
                        'stock_quantity',
                        $item->quantity
                    );

                    /*
                     * Make the product available again
                     * after cancelled stock is restored.
                     */
                    if (
                        $product->status === 'sold_out' &&
                        $product->fresh()->stock_quantity > 0
                    ) {
                        $product->update([
                            'status' => 'available'
                        ]);
                    }
                }
            }

            $order->update([
                'status' => 'cancelled'
            ]);
        });

        \App\Services\OrderNotificationService::notifyStatusChange($order, 'cancelled');

        return response()->json([
            'message' =>
                'Order cancelled successfully and stock restored.'
        ], 200);
    }
}