<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FarmerProductController extends Controller
{
    /**
     * List all products owned by the authenticated farmer
     */
    public function index(Request $request): JsonResponse
    {
        $products = Product::where('farmer_id', $request->user()->id)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($products, 200);
    }

    /**
     * Create a new product
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $validated['farmer_id'] = $request->user()->id;

        // If stock is 0, automatically mark product as sold out
        if (
            isset($validated['stock_quantity']) &&
            $validated['stock_quantity'] == 0
        ) {
            $validated['status'] = 'sold_out';
        }

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully.',
            'product' => $product->load('category'),
        ], 201);
    }

    /**
     * View a specific product owned by the farmer
     */
    public function show(Request $request, $id): JsonResponse
    {
        $product = Product::where('farmer_id', $request->user()->id)
            ->with('category')
            ->find($id);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found.'
            ], 404);
        }

        return response()->json([
            'product' => $product
        ], 200);
    }

    /**
     * Update product details or inventory
     */
    public function update(
        UpdateProductRequest $request,
        $id
    ): JsonResponse {
        $product = Product::where(
            'farmer_id',
            $request->user()->id
        )->find($id);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found.'
            ], 404);
        }

        $validated = $request->validated();

        // Automatically mark as sold out when stock becomes 0
        if (
            isset($validated['stock_quantity']) &&
            $validated['stock_quantity'] == 0
        ) {
            $validated['status'] = 'sold_out';
        }

        // If stock becomes greater than 0 and status was sold_out,
        // make the product available again unless another status is provided.
        if (
            isset($validated['stock_quantity']) &&
            $validated['stock_quantity'] > 0 &&
            ! isset($validated['status']) &&
            $product->status === 'sold_out'
        ) {
            $validated['status'] = 'available';
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully.',
            'product' => $product->load('category'),
        ], 200);
    }

    /**
     * Delete a product
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $product = Product::where(
            'farmer_id',
            $request->user()->id
        )->find($id);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found.'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.'
        ], 200);
    }
}