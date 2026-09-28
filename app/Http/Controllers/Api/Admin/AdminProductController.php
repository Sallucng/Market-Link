<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    /**
     * List all products (admin view) with pagination.
     */
    public function index(): JsonResponse
    {
        $products = Product::with(['farmerProfile.user', 'category'])
            ->latest()
            ->paginate(request()->input('per_page', 20));

        return response()->json(['status' => 'success', 'data' => $products]);
    }

    /**
     * Store a new product.
     */
    public function store(AdminProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product created successfully.',
            'data'    => $product,
        ], 201);
    }

    /**
     * Show a single product.
     */
    public function show($id): JsonResponse
    {
        $product = Product::with(['farmerProfile.user', 'category'])->find($id);

        if (! $product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $product]);
    }

    /**
     * Update an existing product.
     */
    public function update($id, AdminProductRequest $request): JsonResponse
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
        }

        $data = $request->validated();

        if ($request->hasFile('image')) {
            // delete old image if present
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product updated successfully.',
            'data'    => $product,
        ]);
    }

    /**
     * Delete a product.
     */
    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
        }

        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Product removed successfully.',
        ]);
    }
}
