<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\OrderNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function show()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('warning', 'Your pre-order cart is empty.');
        }

        // Group items by farmer
        $groupedCart = [];
        $total = 0;
        foreach ($cart as $id => $item) {
            $farmerId = $item['farmer_id'];
            if (!isset($groupedCart[$farmerId])) {
                $farmer = Farmer::with('market', 'user')->find($farmerId);
                $groupedCart[$farmerId] = [
                    'farmer' => $farmer,
                    'farmer_name' => $farmer ? $farmer->stall_name : $item['farmer_name'],
                    'contact_person' => $farmer ? ($farmer->contact_person ?: ($farmer->user->name ?? 'Grower')) : 'Grower',
                    'contact_number' => $farmer ? ($farmer->contact_number ?: ($farmer->user->contact_number ?? 'N/A')) : 'N/A',
                    'stall_address' => $farmer ? $farmer->address : 'Stall Counter',
                    'market_name' => $farmer && $farmer->market ? $farmer->market->name : $item['market_name'],
                    'market_address' => $farmer && $farmer->market ? ($farmer->market->address . ', ' . $farmer->market->city) : 'Local Market Plaza',
                    'pickup_time_windows' => $farmer ? $farmer->pickup_time_windows : $item['pickup_time_windows'],
                    'operating_days' => $farmer ? $farmer->operating_days : $item['operating_days'],
                    'cutoff_hours' => $farmer ? $farmer->cutoff_hours : $item['cutoff_hours'],
                    'items' => [],
                ];
            }
            $groupedCart[$farmerId]['items'][$id] = $item;
            $total += $item['price'] * $item['quantity'];
        }

        $customer = Auth::user();

        return view('customer.checkout', compact('groupedCart', 'total', 'customer'));
    }

    public function placeOrder(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('warning', 'Your pre-order cart is empty.');
        }

        $request->validate([
            'pickup_date' => 'required|array',
            'pickup_date.*' => 'required|date|after_or_equal:today',
            'pickup_time_slot' => 'required|array',
            'pickup_time_slot.*' => 'required|string',
            'notes' => 'nullable|array',
        ]);

        $user = Auth::user();
        $placedOrders = [];

        DB::beginTransaction();
        try {
            // Group cart by farmer
            $byFarmer = [];
            foreach ($cart as $id => $item) {
                $byFarmer[$item['farmer_id']][] = $item;
            }

            foreach ($byFarmer as $farmerId => $items) {
                $farmer = Farmer::with(['market', 'user'])->findOrFail($farmerId);

                if (!$farmer->is_approved || !$farmer->user?->is_active) {
                    throw new \Exception("Stall '{$farmer->stall_name}' is currently unavailable for pre-orders.");
                }

                $pickupDate = $request->input("pickup_date.{$farmerId}");
                $pickupSlot = $request->input("pickup_time_slot.{$farmerId}");
                $notes = $request->input("notes.{$farmerId}", '');

                $farmerTotal = 0;
                foreach ($items as $item) {
                    $farmerTotal += $item['price'] * $item['quantity'];
                }

                // Calculate cutoff time (default 2 hours before 8 AM on pickup date)
                $cutoffHours = $farmer->cutoff_hours ?: 2;
                $cutoffTime = Carbon::parse($pickupDate)->setTime(8, 0)->subHours($cutoffHours);

                $order = Order::create([
                    'customer_id' => $user->id,
                    'farmer_id' => $farmer->id,
                    'market_id' => $farmer->market_id,
                    'order_number' => 'ML-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                    'order_status' => 'placed',
                    'pickup_date' => $pickupDate,
                    'pickup_time_slot' => $pickupSlot,
                    'total_amount' => $farmerTotal,
                    'payment_method' => 'pay_at_pickup',
                    'cutoff_time' => $cutoffTime,
                    'notes' => $notes,
                ]);

                foreach ($items as $item) {
                    $product = Product::lockForUpdate()->find($item['id']);
                    if (!$product || $product->stock_quantity < $item['quantity']) {
                        $available = $product ? $product->stock_quantity : 0;
                        throw new \Exception("Product '{$item['name']}' has insufficient stock (Requested: {$item['quantity']}, Available: {$available}). Please update your cart.");
                    }

                    $product->stock_quantity -= $item['quantity'];
                    if ($product->stock_quantity == 0) {
                        $product->is_sold_out = true;
                    }
                    $product->save();

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);
                }

                // Notify customer
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Pre-Order Confirmation #' . $order->order_number,
                    'message' => "Your pre-order for {$farmer->stall_name} has been placed. Selected Pickup: {$order->pickup_date->format('M d, Y')} ({$order->pickup_time_slot}). Settlement is due in person at pickup.",
                    'type' => 'order',
                ]);

                // Notify farmer
                if ($farmer->user_id) {
                    Notification::create([
                        'user_id' => $farmer->user_id,
                        'title' => 'New Pre-Order #' . $order->order_number,
                        'message' => "New pre-order received from {$user->name} (" . count($items) . " items, $" . number_format($farmerTotal, 2) . "). Scheduled Pickup: {$order->pickup_date->format('M d, Y')} ({$order->pickup_time_slot}).",
                        'type' => 'order',
                    ]);
                }

                $placedOrders[] = $order;
            }

            DB::commit();
            session()->forget('cart');

            // Dispatch Order Confirmation & Notification Emails (Customer, Farmer, Admin)
            foreach ($placedOrders as $order) {
                OrderNotificationService::notifyOrderPlaced($order);
            }

            return redirect()->route('customer.orders.index')
                ->with('success', 'Your pre-order was successfully placed! Remember to pay the farmer in person when collecting your products at the stall.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
}
