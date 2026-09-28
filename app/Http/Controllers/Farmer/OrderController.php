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

        if (in_array($order->order_status, ['cancelled', 'completed', 'declined'])) {
            return back()->with('error', "Cannot update order #{$order->order_number} because it has already been {$order->order_status}.");
        }

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
            $order->decline_reason = $validated['reason'] ?? 'Stall inventory unavailable.';
            $order->payment_status = 'declined';
        } elseif ($newStatus === 'completed') {
            $order->payment_status = 'paid';
        }

        $order->order_status = $newStatus;
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

    public function export(Request $request)
    {
        $farmer = $this->getFarmer();
        $query = Order::where('farmer_id', $farmer->id)->with(['customer', 'items.product']);

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('pickup_date', $request->date);
        }

        $orders = $query->latest()->get();
        $filename = "marketlink-stall-orders-{$farmer->id}-" . date('Y-m-d_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Order Number',
                'Customer Name',
                'Contact Phone',
                'Email',
                'Pickup Date',
                'Pickup Time Slot',
                'Order Status',
                'Payment Status',
                'Total Amount ($)',
                'Items Summary',
                'Order Placed Date',
            ]);

            foreach ($orders as $order) {
                $itemsList = $order->items->map(function ($item) {
                    return "{$item->quantity}x " . ($item->product->name ?? 'Harvest Item');
                })->implode('; ');

                fputcsv($handle, [
                    $order->order_number,
                    $order->customer->name ?? 'Guest Shopper',
                    $order->customer->contact_number ?? 'N/A',
                    $order->customer->email ?? 'N/A',
                    $order->pickup_date ? $order->pickup_date->format('Y-m-d') : 'N/A',
                    $order->pickup_time_slot,
                    $order->order_status,
                    $order->payment_status,
                    number_format($order->total_amount, 2),
                    $itemsList,
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

