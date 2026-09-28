<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * View all items in the customer's cart
     */
    public function index(Request $request): JsonResponse
    {
        $cartItems = Cart::where('customer_id', $request->user()->id)
            ->with([
                'product.farmer.farmerProfile',
                'product.category'
            ])
            ->get();

        $grandTotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return response()->json([
            'cart_items' => $cartItems,
            'grand_total' => round($grandTotal, 2),
        ], 200);
    }

    /**
     * Add a product to the cart
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        $product = Product::with('farmer')
            ->find($request->product_id);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found.'
            ], 404);
        }

        if ($product->status !== 'available') {
            return response()->json([
                'message' => 'This product is currently unavailable.'
            ], 400);
        }

        if (! $product->farmer || ! $product->farmer->is_active) {
            return response()->json([
                'message' => 'This farmer is currently inactive.'
            ], 400);
        }

        if ($product->stock_quantity < $request->quantity) {
            return response()->json([
                'message' => "Only {$product->stock_quantity} unit(s) available in stock."
            ], 400);
        }

        $cartItem = Cart::where('customer_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;

            if ($product->stock_quantity < $newQuantity) {
                return response()->json([
                    'message' => "Cannot add more. Stock limit of {$product->stock_quantity} exceeded."
                ], 400);
            }

            $cartItem->update([
                'quantity' => $newQuantity
            ]);
        } else {
            $cartItem = Cart::create([
                'customer_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'message' => 'Item added to cart successfully.',
            'cart_item' => $cartItem->load([
                'product.category',
                'product.farmer.farmerProfile'
            ]),
        ], 201);
    }

    /**
     * Update cart item quantity
     */
    public function update(
        UpdateCartRequest $request,
        $id
    ): JsonResponse {
        $cartItem = Cart::where(
            'customer_id',
            $request->user()->id
        )->with('product.farmer')->find($id);

        if (! $cartItem) {
            return response()->json([
                'message' => 'Cart item not found.'
            ], 404);
        }

        if ($cartItem->product->status !== 'available') {
            return response()->json([
                'message' => 'This product is currently unavailable.'
            ], 400);
        }

        if (
            ! $cartItem->product->farmer ||
            ! $cartItem->product->farmer->is_active
        ) {
            return response()->json([
                'message' => 'This farmer is currently inactive.'
            ], 400);
        }

        if (
            $cartItem->product->stock_quantity <
            $request->quantity
        ) {
            return response()->json([
                'message' =>
                    "Only {$cartItem->product->stock_quantity} unit(s) available in stock."
            ], 400);
        }

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'message' => 'Cart updated successfully.',
            'cart_item' => $cartItem->load([
                'product.category',
                'product.farmer.farmerProfile'
            ]),
        ], 200);
    }

    /**
     * Remove a single item from the cart
     */
    public function destroy(
        Request $request,
        $id
    ): JsonResponse {
        $cartItem = Cart::where(
            'customer_id',
            $request->user()->id
        )->find($id);

        if (! $cartItem) {
            return response()->json([
                'message' => 'Cart item not found.'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Item removed from cart.'
        ], 200);
    }

    /**
     * Clear customer's cart
     */
    public function clear(Request $request): JsonResponse
    {
        Cart::where(
            'customer_id',
            $request->user()->id
        )->delete();

        return response()->json([
            'message' => 'Cart cleared successfully.'
        ], 200);
    }
}