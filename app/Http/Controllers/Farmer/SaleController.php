<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    protected function getFarmer()
    {
        $farmer = Farmer::where('user_id', Auth::id())->first();
        if (!$farmer) {
            abort(403, 'Farmer profile not found.');
        }
        return $farmer;
    }

    public function index()
    {
        $farmer = $this->getFarmer();
        $sales = Sale::where('farmer_id', $farmer->id)
            ->withCount('products')
            ->latest()
            ->paginate(10);

        return view('farmer.sales.index', compact('farmer', 'sales'));
    }

    public function create()
    {
        $farmer = $this->getFarmer();
        $products = Product::where('farmer_id', $farmer->id)
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        return view('farmer.sales.create', compact('farmer', 'products'));
    }

    public function store(Request $request)
    {
        $farmer = $this->getFarmer();

        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:1000',
            'discount_percentage' => 'nullable|integer|min:1|max:99',
            'badge_label' => 'nullable|string|max:40',
            'banner_image' => 'nullable|url|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $sale = Sale::create([
            'farmer_id' => $farmer->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'discount_percentage' => $validated['discount_percentage'] ?? null,
            'badge_label' => $validated['badge_label'] ?? null,
            'banner_image' => $validated['banner_image'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->has('is_active'),
            'is_featured' => false,
        ]);

        if (!empty($validated['product_ids'])) {
            $validProductIds = Product::where('farmer_id', $farmer->id)
                ->whereIn('id', $validated['product_ids'])
                ->pluck('id')
                ->toArray();
            $sale->products()->sync($validProductIds);
        }

        return redirect()->route('farmer.sales.index')->with('success', 'Promotional sale created successfully!');
    }

    public function edit($id)
    {
        $farmer = $this->getFarmer();
        $sale = Sale::where('farmer_id', $farmer->id)->with('products')->findOrFail($id);
        $products = Product::where('farmer_id', $farmer->id)
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        return view('farmer.sales.edit', compact('farmer', 'sale', 'products'));
    }

    public function update(Request $request, $id)
    {
        $farmer = $this->getFarmer();
        $sale = Sale::where('farmer_id', $farmer->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:1000',
            'discount_percentage' => 'nullable|integer|min:1|max:99',
            'badge_label' => 'nullable|string|max:40',
            'banner_image' => 'nullable|url|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $sale->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'discount_percentage' => $validated['discount_percentage'] ?? null,
            'badge_label' => $validated['badge_label'] ?? null,
            'banner_image' => $validated['banner_image'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->has('is_active'),
        ]);

        $validProductIds = [];
        if (!empty($validated['product_ids'])) {
            $validProductIds = Product::where('farmer_id', $farmer->id)
                ->whereIn('id', $validated['product_ids'])
                ->pluck('id')
                ->toArray();
        }
        $sale->products()->sync($validProductIds);

        return redirect()->route('farmer.sales.index')->with('success', 'Promotional sale updated successfully!');
    }

    public function toggle($id)
    {
        $farmer = $this->getFarmer();
        $sale = Sale::where('farmer_id', $farmer->id)->findOrFail($id);
        $sale->is_active = !$sale->is_active;
        $sale->save();

        $status = $sale->is_active ? 'activated' : 'paused';
        return redirect()->back()->with('success', "Sale campaign '{$sale->title}' has been {$status}.");
    }

    public function destroy($id)
    {
        $farmer = $this->getFarmer();
        $sale = Sale::where('farmer_id', $farmer->id)->findOrFail($id);
        $title = $sale->title;
        $sale->delete();

        return redirect()->route('farmer.sales.index')->with('success', "Sale '{$title}' removed successfully.");
    }
}
