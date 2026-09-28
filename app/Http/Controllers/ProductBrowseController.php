<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductBrowseController extends Controller
{
    /**
     * List all active categories
     */
    public function categories(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'categories' => $categories
        ], 200);
    }

    /**
     * Search and filter available products
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::where(function ($q) {
                $q->where('status', 'available')
                  ->orWhereNull('status');
            })
            ->where('is_sold_out', false)
            ->whereHas('farmer', function ($farmerQuery) {
                $farmerQuery->whereHas('user', function ($uq) {
                    $uq->where('role', 'farmer')->where('status', 'active');
                });
            })
            ->whereHas('category', function ($categoryQuery) {
                $categoryQuery->where('is_active', true);
            })
            ->with([
                'farmer.user',
                'category'
            ]);

        // Search by product name or description
        if ($request->filled('search')) {
            $search = $request->query('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->query('category_id')
            );
        }

        // Filter by farmer
        if ($request->filled('farmer_id')) {
            $query->where(
                'farmer_id',
                $request->query('farmer_id')
            );
        }

        // Minimum price
        if ($request->filled('min_price')) {
            $query->where(
                'price',
                '>=',
                $request->query('min_price')
            );
        }

        // Maximum price
        if ($request->filled('max_price')) {
            $query->where(
                'price',
                '<=',
                $request->query('max_price')
            );
        }

        $products = $query
            ->orderBy('name')
            ->paginate(15);

        return response()->json($products, 200);
    }

    /**
     * Show single available product with farmer information
     */
    public function show($id): JsonResponse
    {
        $product = Product::where(function ($q) {
                $q->where('status', 'available')
                  ->orWhereNull('status');
            })
            ->where('is_sold_out', false)
            ->whereHas('farmer', function ($query) {
                $query->whereHas('user', function ($uq) {
                    $uq->where('role', 'farmer')->where('status', 'active');
                });
            })
            ->with([
                'farmer.user',
                'category'
            ])
            ->find($id);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found or unavailable.'
            ], 404);
        }

        return response()->json([
            'product' => $product
        ], 200);
    }
}