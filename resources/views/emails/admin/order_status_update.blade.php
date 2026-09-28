<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Order Notification</title>
</head>
<body style="margin: 0; padding: 24px; background-color: #f7f9f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <div style="max-width: 620px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);">
        
        <!-- Header -->
        <div style="background-color: #0b2116; padding: 28px 32px; text-align: center;">
            <div style="display: inline-block; padding: 4px 12px; background: rgba(255, 255, 255, 0.15); border-radius: 999px; font-size: 11px; font-weight: 700; color: #a7f3d0; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px;">
                Platform Admin Alert
            </div>
            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 700;">MarketLink Administration</h1>
            <p style="margin: 4px 0 0 0; color: #d8f3dc; font-size: 13px;">Real-Time Order Lifecycle Monitor</p>
        </div>

        <!-- Body -->
        <div style="padding: 32px 28px;">
            
            <!-- Status Badge Box -->
            @if($status === 'placed' || $status === 'pending')
                <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 6px; padding: 16px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 15px; font-weight: 700; color: #166534;">
                        🛒 New Pre-Order Placed: #{{ $order->order_number }}
                    </p>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #15803d; line-height: 1.5;">
                        A customer has placed a new harvest pre-order for ${{ number_format($order->total_amount, 2) }}.
                    </p>
                </div>
            @elseif($status === 'accepted')
                <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 6px; padding: 16px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 15px; font-weight: 700; color: #1e40af;">
                        🌿 Pre-Order Confirmed by Grower: #{{ $order->order_number }}
                    </p>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #1d4ed8; line-height: 1.5;">
                        The grower accepted this pre-order and locked it in for harvest packing.
                    </p>
                </div>
            @elseif($status === 'ready_for_pickup')
                <div style="background-color: #ecfdf5; border-left: 4px solid #10b981; border-radius: 6px; padding: 16px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 15px; font-weight: 700; color: #065f46;">
                        🧺 Order Ready for Pickup at Stall: #{{ $order->order_number }}
                    </p>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #047857; line-height: 1.5;">
                        The produce has been packed and is ready at the market stall.
                    </p>
                </div>
            @elseif($status === 'completed')
                <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 6px; padding: 16px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 15px; font-weight: 700; color: #166534;">
                        🎉 Pre-Order Completed & Settled: #{{ $order->order_number }}
                    </p>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #15803d; line-height: 1.5;">
                        The customer has picked up the order and settlement was confirmed.
                    </p>
                </div>
            @elseif($status === 'declined')
                <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; border-radius: 6px; padding: 16px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 15px; font-weight: 700; color: #991b1b;">
                        ⚠️ Pre-Order Declined by Grower: #{{ $order->order_number }}
                    </p>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #b91c1c; line-height: 1.5;">
                        {{ $reason ? "Reason: {$reason}" : ($order->decline_reason ? "Reason: {$order->decline_reason}" : "Item inventory unavailable.") }}
                    </p>
                </div>
            @elseif($status === 'cancelled')
                <div style="background-color: #f8fafc; border-left: 4px solid #64748b; border-radius: 6px; padding: 16px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 15px; font-weight: 700; color: #334155;">
                        🚫 Pre-Order Cancelled: #{{ $order->order_number }}
                    </p>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #475569; line-height: 1.5;">
                        This pre-order was cancelled. Reserved stall inventory has been released.
                    </p>
                </div>
            @else
                <div style="background-color: #f8fafc; border-left: 4px solid #64748b; border-radius: 6px; padding: 16px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 15px; font-weight: 700; color: #334155;">
                        Pre-Order Status: {{ ucfirst(str_replace('_', ' ', $status)) }} (#{{ $order->order_number }})
                    </p>
                </div>
            @endif

            <h2 style="margin: 0 0 16px 0; font-size: 17px; color: #0f172a;">Order Lifecycle Overview</h2>

            <!-- Order Details Table -->
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; margin-bottom: 24px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; width: 140px;">Order Number:</td>
                        <td style="padding: 6px 0; font-weight: 700; color: #0f172a;">#{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Customer:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #0f172a;">
                            {{ $order->customer?->name }} 
                            <span style="font-weight: 400; color: #64748b;">({{ $order->customer?->email }})</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Farmer Stall:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #0f172a;">
                            {{ $order->farmer?->stall_name }}
                            <span style="font-weight: 400; color: #64748b;">({{ $order->farmer?->user?->email }})</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Market Location:</td>
                        <td style="padding: 6px 0; color: #0f172a;">
                            {{ $order->farmer?->market?->name ?? 'Market' }} ({{ $order->farmer?->address ?? 'Stall' }})
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Pickup Schedule:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #1b4332;">
                            {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') : 'Market Day' }} &bull; {{ $order->pickup_time_slot ?? 'Market Hours' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Order Total:</td>
                        <td style="padding: 6px 0; font-weight: 700; color: #1b4332; font-size: 15px;">
                            ${{ number_format($order->total_amount, 2) }}
                            <span style="font-weight: 400; font-size: 12px; color: #64748b;">(Status: {{ ucfirst(str_replace('_', ' ', $status)) }})</span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Ordered Items List -->
            @if($order->items && count($order->items) > 0)
                <h3 style="margin: 0 0 12px 0; font-size: 15px; color: #0f172a;">Ordered Items Breakdown</h3>
                <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background-color: #f1f5f9; text-align: left; color: #475569;">
                                <th style="padding: 10px 14px;">Item</th>
                                <th style="padding: 10px 14px; text-align: center;">Qty</th>
                                <th style="padding: 10px 14px; text-align: right;">Unit Price</th>
                                <th style="padding: 10px 14px; text-align: right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr style="border-top: 1px solid #e2e8f0;">
                                    <td style="padding: 10px 14px; font-weight: 600; color: #1e293b;">
                                        {{ $item->product?->name ?? 'Harvest Item' }}
                                    </td>
                                    <td style="padding: 10px 14px; text-align: center; color: #475569;">
                                        {{ $item->quantity }}
                                    </td>
                                    <td style="padding: 10px 14px; text-align: right; color: #475569;">
                                        ${{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td style="padding: 10px 14px; text-align: right; font-weight: 600; color: #0f172a;">
                                        ${{ number_format($item->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Admin Actions -->
            <div style="text-align: center; margin: 28px 0 12px 0;">
                <a href="{{ url('/admin/dashboard') }}" style="display: inline-block; background-color: #1b4332; color: #ffffff; padding: 12px 28px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px;">
                    Open Admin Dashboard &rarr;
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f1f5f9; padding: 16px 24px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b;">
            MarketLink Platform Administration &bull; Automated System Notice &bull; CAN-SPAM Compliant
        </div>
    </div>
</body>
</html>
