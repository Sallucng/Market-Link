<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('customer.cart', compact('cart', 'total'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::with(['farmer.market', 'farmer.user'])->findOrFail($id);

        if (!$product->farmer || !$product->farmer->is_approved || !$product->farmer->user?->is_active) {
            return back()->with('error', 'This stall is currently inactive or not approved for pre-orders.');
        }

        if (!$product->is_available || $product->is_sold_out || $product->stock_quantity <= 0) {
            return back()->with('error', 'Sorry, this product is currently sold out.');
        }

        $quantity = max(1, (int)$request->input('quantity', 1));

        if ($quantity > $product->stock_quantity) {
            return back()->with('error', "Only {$product->stock_quantity} {$product->unit} available in stock.");
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $newQty = $cart[$id]['quantity'] + $quantity;
            if ($newQty > $product->stock_quantity) {
                return back()->with('error', "Cannot add more. Reached maximum available stock ({$product->stock_quantity}).");
            }
            $cart[$id]['quantity'] = $newQty;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float)$product->price,
                'unit' => $product->unit,
                'image_url' => $product->image_url,
                'quantity' => $quantity,
                'farmer_id' => $product->farmer_id,
                'farmer_name' => $product->farmer->stall_name,
                'market_id' => $product->farmer->market_id,
                'market_name' => $product->farmer->market->name ?? 'Local Market',
                'pickup_time_windows' => $product->farmer->pickup_time_windows,
                'operating_days' => $product->farmer->operating_days,
                'cutoff_hours' => $product->farmer->cutoff_hours,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', "Added {$product->name} to your pre-order cart!");
    }

    public function update(Request $request, $id)
    {
        $quantity = (int)$request->input('quantity', 1);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $product = Product::find($id);
            if ($product && $quantity > $product->stock_quantity) {
                return back()->with('error', "Maximum available stock is {$product->stock_quantity}.");
            }

            if ($quantity > 0) {
                $cart[$id]['quantity'] = $quantity;
                session()->put('cart', $cart);
                return back()->with('success', 'Cart updated successfully.');
            } else {
                unset($cart[$id]);
                session()->put('cart', $cart);
                return back()->with('info', 'Item removed from cart.');
            }
        }

        return back();
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Item removed from pre-order cart.');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('info', 'Pre-order cart cleared.');
    }
}
