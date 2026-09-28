<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Farmer;
use App\Models\Favorite;
use App\Models\Notification;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected function getFarmer()
    {
        $farmer = Farmer::where('user_id', Auth::id())->first();
        if (!$farmer) {
            abort(403, 'Farmer profile not found.');
        }
        return $farmer;
    }

    public function index(Request $request)
    {
        $farmer = $this->getFarmer();
        $query = Product::where('farmer_id', $farmer->id)->with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('name', 'like', "%{$q}%");
        }

        $products = $query->latest()->paginate(10);
        $categories = Category::orderBy('name')->get();

        return view('farmer.products.index', compact('farmer', 'products', 'categories'));
    }

    public function create()
    {
        $farmer = $this->getFarmer();
        $categories = Category::orderBy('name')->get();
        return view('farmer.products.create', compact('farmer', 'categories'));
    }

    public function store(Request $request)
    {
        $farmer = $this->getFarmer();

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:30',
            'stock_quantity' => 'required|integer|min:0',
            'weekly_recurring_stock' => 'nullable|integer|min:0',
            'image_url' => 'nullable|url|max:500',
        ]);

        $validated['farmer_id'] = $farmer->id;
        $validated['is_sold_out'] = $validated['stock_quantity'] == 0;
        $validated['is_available'] = true;
        $validated['weekly_recurring_stock'] = $validated['weekly_recurring_stock'] ?? $validated['stock_quantity'];

        if (empty($validated['image_url'])) {
            $validated['image_url'] = 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?auto=format&fit=crop&w=600&q=80';
        }

        Product::create($validated);

        return redirect()->route('farmer.products.index')->with('success', 'Farm product listed successfully!');
    }

    public function edit($id)
    {
        $farmer = $this->getFarmer();
        $product = Product::where('farmer_id', $farmer->id)->findOrFail($id);
        $categories = Category::orderBy('name')->get();

        return view('farmer.products.edit', compact('farmer', 'product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $farmer = $this->getFarmer();
        $product = Product::where('farmer_id', $farmer->id)->findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:30',
            'stock_quantity' => 'required|integer|min:0',
            'weekly_recurring_stock' => 'nullable|integer|min:0',
            'image_url' => 'nullable|url|max:500',
            'is_available' => 'nullable|boolean',
        ]);

        $wasSoldOut = $product->is_sold_out || $product->stock_quantity == 0;
        $validated['is_sold_out'] = $validated['stock_quantity'] == 0;
        $validated['is_available'] = $request->has('is_available');

        $product->update($validated);

        if ($wasSoldOut && !$product->is_sold_out && $product->stock_quantity > 0) {
            $this->dispatchRestockAlerts($product, $farmer);
        }

        return redirect()->route('farmer.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $farmer = $this->getFarmer();
        $product = Product::where('farmer_id', $farmer->id)->findOrFail($id);

        $hasActiveOrders = OrderItem::where('product_id', $product->id)
            ->whereHas('order', function ($q) {
                $q->whereIn('order_status', ['placed', 'accepted', 'ready_for_pickup', 'pending', 'ready']);
            })->exists();

        if ($hasActiveOrders) {
            return back()->with('error', "Cannot delete '{$product->name}' because it is included in active customer pre-orders. Mark it as sold out or unavailable instead.");
        }

        $product->delete();

        return back()->with('success', 'Product removed from your stall inventory.');
    }

    public function toggleSoldOut($id)
    {
        $farmer = $this->getFarmer();
        $product = Product::where('farmer_id', $farmer->id)->findOrFail($id);

        $product->is_sold_out = !$product->is_sold_out;
        if ($product->is_sold_out) {
            $product->stock_quantity = 0;
        } else {
            if ($product->stock_quantity == 0) {
                $product->stock_quantity = $product->weekly_recurring_stock ?: 5;
            }
            $this->dispatchRestockAlerts($product, $farmer);
        }
        $product->save();

        $status = $product->is_sold_out ? 'marked as Sold Out' : 'marked as In Stock';
        return back()->with('success', "{$product->name} is now {$status}.");
    }

    public function replenishFromTemplate()
    {
        $farmer = $this->getFarmer();
        $products = Product::where('farmer_id', $farmer->id)->get();

        $replenishedCount = 0;
        foreach ($products as $product) {
            if ($product->weekly_recurring_stock > 0) {
                $wasSoldOut = $product->is_sold_out || $product->stock_quantity == 0;
                $product->stock_quantity = $product->weekly_recurring_stock;
                $product->is_sold_out = false;
                $product->is_available = true;
                $product->save();

                if ($wasSoldOut) {
                    $this->dispatchRestockAlerts($product, $farmer);
                }
                $replenishedCount++;
            }
        }

        return back()->with('success', "Weekly Harvest Template Applied: {$replenishedCount} products replenished for upcoming market day!");
    }

    protected function dispatchRestockAlerts(Product $product, Farmer $farmer): void
    {
        $favoritedCustomerIds = Favorite::where('item_type', 'product')
            ->where('item_id', $product->id)
            ->pluck('customer_id');

        foreach ($favoritedCustomerIds as $customerId) {
            Notification::firstOrCreate([
                'user_id' => $customerId,
                'title' => "Restock Alert: {$product->name}",
                'message' => "{$product->name} is back in stock at {$farmer->stall_name}! Reserve your pre-order now.",
                'type' => 'restock',
                'created_at' => now(),
            ]);
        }
    }
}
