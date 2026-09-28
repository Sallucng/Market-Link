<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    /**
     * List all system-wide product categories.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::withCount('products');

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'data'   => $categories,
        ]);
    }

    /**
     * Store new product category.
     */
    public function store(AdminCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create([
            'name'        => $validated['name'],
            'slug'        => $validated['slug'] ?? Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'image'       => $imagePath,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Category created successfully.',
            'data'    => $category,
        ], 201);
    }

    /**
     * Show single product category with associated products count.
     */
    public function show($id): JsonResponse
    {
        $category = Category::withCount('products')->find($id);

        if (!$category) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Category not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $category,
        ]);
    }

    /**
     * Update product category.
     */
    public function update($id, AdminCategoryRequest $request): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Category not found.',
            ], 404);
        }

        $validated = $request->validated();
        $updateData = collect($validated)->except(['image'])->toArray();

        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $updateData['image'] = $request->file('image')->store('categories', 'public');
        }

        if (isset($validated['name']) && $validated['name'] !== $category->name && !isset($validated['slug'])) {
            $updateData['slug'] = Str::slug($validated['name']);
        }

        $category->update($updateData);

        return response()->json([
            'status'  => 'success',
            'message' => 'Category updated successfully.',
            'data'    => $category,
        ]);
    }

    /**
     * Delete product category if no products are associated.
     */
    public function destroy($id): JsonResponse
    {
        $category = Category::withCount('products')->find($id);

        if (!$category) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Category not found.',
            ], 404);
        }

        if ($category->products_count > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => "Cannot delete category with {$category->products_count} associated product(s). Please reassign or delete products first.",
            ], 422);
        }

        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Category deleted successfully.',
        ]);
    }
}
