<?php

namespace App\Http\Controllers\Api\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\ProductStoreRequest;
use App\Http\Requests\Farmer\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FarmerProductController extends Controller
{
    /**
     * List farmer's products with filtering & pagination.
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

        $query = Product::where('farmer_profile_id', $profile->id)->with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_available')) {
            $query->where('is_available', filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    /**
     * Create a new product.
     */
    public function store(ProductStoreRequest $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $weeklyQuota = $validated['weekly_quota'] ?? $validated['weekly_stock'] ?? $validated['stock_quantity'];

        $product = Product::create([
            'farmer_profile_id' => $profile->id,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'unit' => $validated['unit'],
            'stock_quantity' => $validated['stock_quantity'],
            'weekly_quota' => $weeklyQuota,
            'is_available' => $request->boolean('is_available', true),
            'image_path' => $imagePath,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully.',
            'data' => $product->load('category'),
        ], 201);
    }

    /**
     * Show single product detail.
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

        $product = Product::where('farmer_profile_id', $profile->id)->with('category')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $product,
        ]);
    }

    /**
     * Update product details & image handling.
     */
    public function update($id, ProductUpdateRequest $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $product = Product::where('farmer_profile_id', $profile->id)->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.',
            ], 404);
        }

        $validated = $request->validated();
        $updateData = collect($validated)->except(['image'])->toArray();

        if ($request->hasFile('image')) {
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
            $updateData['image_path'] = $request->file('image')->store('products', 'public');
        }

        if (isset($validated['name']) && $validated['name'] !== $product->name && !isset($validated['slug'])) {
            $updateData['slug'] = Str::slug($validated['name']) . '-' . uniqid();
        }

        if (isset($validated['weekly_quota'])) {
            $updateData['weekly_quota'] = $validated['weekly_quota'];
        } elseif (isset($validated['weekly_stock'])) {
            $updateData['weekly_quota'] = $validated['weekly_stock'];
        }

        $product->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully.',
            'data' => $product->fresh('category'),
        ]);
    }

    /**
     * Delete product.
     */
    public function destroy($id, Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $product = Product::where('farmer_profile_id', $profile->id)->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.',
            ], 404);
        }

        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully.',
        ]);
    }

    /**
     * Toggle product availability / mark sold-out.
     */
    public function toggleAvailability($id, Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $product = Product::where('farmer_profile_id', $profile->id)->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.',
            ], 404);
        }

        $product->is_available = !$product->is_available;
        $product->save();

        return response()->json([
            'status' => 'success',
            'message' => $product->is_available ? 'Product marked as available.' : 'Product marked as unavailable/sold out.',
            'data' => $product,
        ]);
    }

    /**
     * Weekly stock management: update replenishment limit or restock to weekly amount.
     */
    public function updateWeeklyStock($id, Request $request): JsonResponse
    {
        $profile = $request->user()->farmerProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farmer profile not found.',
            ], 404);
        }

        $product = Product::where('farmer_profile_id', $profile->id)->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.',
            ], 404);
        }

        $request->validate([
            'weekly_stock' => ['required', 'integer', 'min:0'],
            'reset_current_stock' => ['nullable', 'boolean'],
        ]);

        $weeklyStock = (int)$request->input('weekly_stock');
        $product->weekly_quota = $weeklyStock;
        if ($request->boolean('reset_current_stock', false)) {
            $product->stock_quantity = $weeklyStock;
            if ($product->stock_quantity > 0) {
                $product->is_available = true;
            }
        }
        $product->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Weekly stock updated successfully.',
            'data' => $product,
        ]);
    }
}
