<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('customer_id', $user->id)
            ->with(['farmer.market', 'items.product', 'review'])
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::where('customer_id', Auth::id())
            ->with(['farmer.market', 'items.product', 'review'])
            ->findOrFail($id);

        return view('customer.orders.show', compact('order'));
    }

    public function cancel($id)
    {
        $order = Order::where('customer_id', Auth::id())
            ->with(['items.product', 'farmer.user', 'customer'])
            ->findOrFail($id);

        if (!$order->canModifyOrCancel()) {
            return back()->with('error', 'This pre-order cannot be cancelled because the cutoff time has passed or the order is already completed.');
        }

        // Restore stock
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->stock_quantity += $item->quantity;
                $item->product->is_sold_out = false;
                $item->product->save();
            }
        }

        $order->order_status = 'cancelled';
        $order->save();

        Notification::create([
            'user_id' => Auth::id(),
            'title' => "Pre-Order #{$order->order_number} Cancelled",
            'message' => "You have cancelled pre-order #{$order->order_number}.",
            'type' => 'order',
        ]);

        if ($order->farmer && $order->farmer->user_id) {
            Notification::create([
                'user_id' => $order->farmer->user_id,
                'title' => "Pre-Order #{$order->order_number} Cancelled",
                'message' => "Customer " . ($order->customer->name ?? 'Shopper') . " cancelled pre-order #{$order->order_number}. Reserved stock has been returned to your stall inventory.",
                'type' => 'order',
            ]);
        }

        // Send Status Update Email to Customer and Admin
        OrderNotificationService::notifyStatusChange($order, 'cancelled');

        return back()->with('success', 'Pre-order has been cancelled.');
    }

    public function modify(Request $request, $id)
    {
        $order = Order::where('customer_id', Auth::id())
            ->with(['farmer.user', 'customer'])
            ->findOrFail($id);

        if (!$order->canModifyOrCancel()) {
            return back()->with('error', 'This pre-order cannot be modified because the cutoff time has passed or the order is already completed.');
        }

        $validated = $request->validate([
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time_slot' => 'required|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $order->update([
            'pickup_date' => $validated['pickup_date'],
            'pickup_time_slot' => $validated['pickup_time_slot'],
            'notes' => $validated['notes'] ?? $order->notes,
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'title' => "Pre-Order #{$order->order_number} Modified",
            'message' => "You have updated the pickup schedule for pre-order #{$order->order_number}.",
            'type' => 'order',
        ]);

        if ($order->farmer && $order->farmer->user_id) {
            $formattedDate = $order->pickup_date ? $order->pickup_date->format('M d, Y') : $validated['pickup_date'];
            Notification::create([
                'user_id' => $order->farmer->user_id,
                'title' => "Pre-Order #{$order->order_number} Rescheduled",
                'message' => "Customer " . ($order->customer->name ?? 'Shopper') . " rescheduled pre-order #{$order->order_number} to {$formattedDate} ({$order->pickup_time_slot}).",
                'type' => 'order',
            ]);
        }

        return back()->with('success', 'Pre-order details updated successfully.');
    }

    public function reorder($id)
    {
        $order = Order::where('customer_id', Auth::id())
            ->with(['items.product.farmer.market'])
            ->findOrFail($id);

        $cart = session()->get('cart', []);
        $addedCount = 0;

        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product && $product->is_available && !$product->is_sold_out && $product->stock_quantity > 0) {
                $cart[$product->id] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float)$product->price,
                    'unit' => $product->unit,
                    'image_url' => $product->image_url,
                    'quantity' => min($item->quantity, $product->stock_quantity),
                    'farmer_id' => $product->farmer_id,
                    'farmer_name' => $product->farmer->stall_name,
                    'market_id' => $product->farmer->market_id,
                    'market_name' => $product->farmer->market->name ?? 'Local Market',
                    'pickup_time_windows' => $product->farmer->pickup_time_windows,
                    'operating_days' => $product->farmer->operating_days,
                    'cutoff_hours' => $product->farmer->cutoff_hours,
                ];
                $addedCount++;
            }
        }

        session()->put('cart', $cart);

        if ($addedCount > 0) {
            return redirect()->route('cart.index')->with('success', "{$addedCount} available items added to your pre-order cart!");
        }

        return back()->with('warning', 'None of the items from that order are currently in stock.');
    }

    public function receipt($id)
    {
        $order = Order::where('customer_id', Auth::id())
            ->with(['farmer.market', 'farmer.user', 'customer', 'items.product'])
            ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('customer.orders.receipt', compact('order'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("MarketLink-Receipt-{$order->order_number}.pdf");
    }
}
