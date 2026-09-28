<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ModerationController extends Controller
{
    public function reviews()
    {
        $reviews = Review::with(['customer', 'farmer', 'product', 'order'])->latest()->paginate(15);
        return view('admin.moderation.reviews', compact('reviews'));
    }

    public function deleteReview($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Inappropriate review was removed from the platform.');
    }

    public function products()
    {
        $products = Product::with(['farmer.user', 'category'])->latest()->paginate(15);
        return view('admin.moderation.products', compact('products'));
    }

    public function toggleProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->is_available = !$product->is_available;
        $product->save();

        $action = $product->is_available ? 'restored' : 'hidden from public storefront';
        return back()->with('info', "Product '{$product->name}' was {$action}.");
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $name = $product->name;

        $hasActiveOrders = OrderItem::where('product_id', $product->id)
            ->whereHas('order', function ($q) {
                $q->whereIn('order_status', ['placed', 'accepted', 'ready_for_pickup', 'pending', 'ready']);
            })->exists();

        if ($hasActiveOrders) {
            $product->is_available = false;
            $product->is_sold_out = true;
            $product->save();
            return back()->with('info', "Product '{$name}' has active pre-orders and has been hidden and marked sold out instead of permanently deleted.");
        }

        $product->delete();

        return back()->with('success', "Inappropriate product listing '{$name}' was removed from the platform.");
    }
}
