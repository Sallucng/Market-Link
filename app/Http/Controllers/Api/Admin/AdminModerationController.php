<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminModerationController extends Controller
{
    /**
     * List all platform products with moderation filter.
     */
    public function products(Request $request): JsonResponse
    {
        $query = Product::with(['farmerProfile.user:id,name,email', 'category']);

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'flagged') {
                $query->where('is_moderated', true);
            } elseif ($status === 'active') {
                $query->where('is_moderated', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate($request->input('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data'   => $products,
        ]);
    }

    /**
     * Moderate product: flag or unpublish inappropriate products, activate, or force delete.
     */
    public function moderateProduct($id, Request $request): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product not found.',
            ], 404);
        }

        if ($request->isMethod('delete') && !$request->has('action')) {
            $request->merge(['action' => 'delete']);
        }

        $validator = Validator::make($request->all(), [
            'action' => ['required', 'in:flag,unflag,delete'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation errors occurred.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $action = $request->input('action');

        if ($action === 'delete') {
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
            $product->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Product removed from platform.',
            ]);
        }

        if ($action === 'flag') {
            $product->is_moderated = true;
            $product->is_available = false;
            $product->status = 'flagged';
        } else {
            $product->is_moderated = false;
        }

        $product->save();

        return response()->json([
            'status'  => 'success',
            'message' => "Product status updated to {$product->status}.",
            'data'    => $product->load(['farmerProfile', 'category']),
        ]);
    }

    /**
     * List all platform customer reviews with moderation filter.
     */
    public function reviews(Request $request): JsonResponse
    {
        $query = Review::with([
            'customer:id,name,email',
            'farmerProfile:id,business_name,stall_number',
            'order:id,pickup_slot,created_at',
            'product:id,name',
        ]);

        if ($request->filled('admin_status')) {
            $status = $request->admin_status;
            if ($status === 'flagged' || $status === 'hidden') {
                $query->where('is_moderated', true);
            } elseif ($status === 'visible') {
                $query->where('is_moderated', false);
            }
        }

        if ($request->filled('rating')) {
            $query->where('rating', (int)$request->rating);
        }

        $reviews = $query->latest()->paginate($request->input('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data'   => $reviews,
        ]);
    }

    /**
     * Moderate review: flag or delete toxic/fake customer reviews.
     */
    public function moderateReview($id, Request $request): JsonResponse
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Review not found.',
            ], 404);
        }

        if ($request->isMethod('delete') && !$request->has('action')) {
            $request->merge(['action' => 'delete']);
        }

        $validator = Validator::make($request->all(), [
            'action' => ['required', 'in:hide,show,flag,delete'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation errors occurred.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $action = $request->input('action');

        if ($action === 'delete') {
            $review->delete();
            return response()->json([
                'status'  => 'success',
                'message' => 'Review permanently deleted.',
            ]);
        }

        switch ($action) {
            case 'hide':
            case 'flag':
                $review->is_moderated = true;
                break;
            case 'show':
                $review->is_moderated = false;
                break;
        }

        $review->save();

        return response()->json([
            'status'  => 'success',
            'message' => "Review moderation status updated to {$review->admin_status}.",
            'data'    => $review,
        ]);
    }
}
