<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
            'description' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
        ]);

        if (empty($validated['icon'])) {
            $validated['icon'] = 'bi-basket';
        }

        Category::create($validated);

        return back()->with('success', "Product category '{$validated['name']}' created.");
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
        ]);

        $category->update($validated);

        return back()->with('success', "Category updated.");
    }

    public function destroy($id)
    {
        $category = Category::withCount('products')->findOrFail($id);
        if ($category->products_count > 0) {
            return back()->with('error', "Cannot delete category '{$category->name}' because it contains {$category->products_count} products.");
        }

        $category->delete();
        return back()->with('success', "Category deleted.");
    }
}
