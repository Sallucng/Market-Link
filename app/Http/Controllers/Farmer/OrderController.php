<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
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
        $query = Order::where('farmer_id', $farmer->id)->with(['customer', 'items.product']);

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('pickup_date', $request->date);
        }

        $orders = $query->latest()->paginate(10);

        $counts = [
            'all' => Order::where('farmer_id', $farmer->id)->count(),
            'placed' => Order::where('farmer_id', $farmer->id)->where('order_status', 'placed')->count(),
            'accepted' => Order::where('farmer_id', $farmer->id)->where('order_status', 'accepted')->count(),
            'ready' => Order::where('farmer_id', $farmer->id)->where('order_status', 'ready_for_pickup')->count(),
            'completed' => Order::where('farmer_id', $farmer->id)->where('order_status', 'completed')->count(),
        ];

        return view('farmer.orders.index', compact('farmer', 'orders', 'counts'));
    }

    public function show($id)
    {
        $farmer = $this->getFarmer();
        $order = Order::where('farmer_id', $farmer->id)
            ->with(['customer', 'items.product', 'review'])
            ->findOrFail($id);

        return view('farmer.orders.show', compact('farmer', 'order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $farmer = $this->getFarmer();
        $order = Order::where('farmer_id', $farmer->id)->with('items.product')->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:accepted,declined,ready_for_pickup,completed',
            'reason' => 'nullable|string|max:255',
        ]);

        $newStatus = $validated['status'];

        if ($newStatus === 'declined') {
            // Restore inventory
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->stock_quantity += $item->quantity;
                    $item->product->is_sold_out = false;
                    $item->product->save();
                }
            }
        }

        $order->order_status = $newStatus;
        if ($newStatus === 'completed') {
            $order->payment_status = 'paid';
        } elseif ($newStatus === 'declined') {
            $order->payment_status = 'declined';
        }
        $order->save();

        // Send In-App notification to Customer per SRS Section 1.6
        $messages = [
            'accepted' => "Your pre-order #{$order->order_number} has been confirmed by {$farmer->stall_name}.",
            'declined' => "Your pre-order #{$order->order_number} was declined by the grower: " . ($validated['reason'] ?? 'Item unavailable.'),
            'ready_for_pickup' => "Great news! Your pre-order #{$order->order_number} is packed and ready for pickup at {$farmer->stall_name} stall.",
            'completed' => "Thank you for visiting! Pre-order #{$order->order_number} has been marked completed. You can now leave a review.",
        ];

        Notification::create([
            'user_id' => $order->customer_id,
            'title' => "Order #{$order->order_number} Update",
            'message' => $messages[$newStatus] ?? "Status changed to {$newStatus}.",
            'type' => 'order',
        ]);

        return back()->with('success', "Order #{$order->order_number} updated to " . ucfirst(str_replace('_', ' ', $newStatus)) . ".");
    }

    public function receipt($id)
    {
        $farmer = $this->getFarmer();
        $order = Order::where('farmer_id', $farmer->id)
            ->with(['farmer.market', 'farmer.user', 'customer', 'items.product'])
            ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('customer.orders.receipt', compact('order'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("MarketLink-Receipt-{$order->order_number}.pdf");
    }
}
