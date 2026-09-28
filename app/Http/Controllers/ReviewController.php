<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Get approved reviews for a farmer
    public function index(Request $request)
    {
        $request->validate([
            'farmer_id' => 'required|exists:users,id',
        ]);

        $reviews = Review::where('farmer_id', $request->farmer_id)
            ->approved()
            ->with(['customer', 'product'])
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Reviews retrieved successfully.',
            'reviews' => $reviews,
        ]);
    }

    // Create a review
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'farmer_id' => 'required|exists:users,id',
            'product_id' => 'nullable|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        // Get the logged-in customer's order
        $order = Order::with('items')
            ->where('id', $request->order_id)
            ->where('customer_id', $request->user()->id)
            ->first();

        // Check order ownership
        if (! $order) {
            return response()->json([
                'message' => 'Order not found or does not belong to you.',
            ], 404);
        }

        // Only completed orders can be reviewed
        if ($order->status !== 'completed') {
            return response()->json([
                'message' => 'You can only review completed orders.',
            ], 422);
        }

        // Check farmer belongs to the order
        if ((int) $order->farmer_id !== (int) $request->farmer_id) {
            return response()->json([
                'message' => 'The selected farmer does not belong to this order.',
            ], 422);
        }

        // If product_id is provided, verify that it exists in this order
        if ($request->filled('product_id')) {
            $productExists = $order->items
                ->contains('product_id', (int) $request->product_id);

            if (! $productExists) {
                return response()->json([
                    'message' => 'The selected product does not belong to this order.',
                ], 422);
            }
        }

        // Prevent duplicate reviews for the same order and product
        $duplicateReview = Review::where('customer_id', $request->user()->id)
            ->where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($duplicateReview) {
            return response()->json([
                'message' => 'You have already reviewed this order and product.',
            ], 409);
        }

        // Create the review
        $review = Review::create([
            'customer_id' => $request->user()->id,
            'order_id' => $request->order_id,
            'farmer_id' => $request->farmer_id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Review created successfully.',
            'review' => $review,
        ], 201);
    }
}