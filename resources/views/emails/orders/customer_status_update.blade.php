<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Update</title>
</head>
<body style="margin: 0; padding: 24px; background-color: #f7f9f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);">
        
        <!-- Header -->
        <div style="background-color: #1b4332; padding: 28px 32px; text-align: center;">
            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700;">MarketLink</h1>
            <p style="margin: 6px 0 0 0; color: #d8f3dc; font-size: 14px;">Pre-Order Status Notification</p>
        </div>

        <!-- Body -->
        <div style="padding: 32px 28px;">
            @if($status === 'ready_for_pickup')
                <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 6px; padding: 16px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 16px; font-weight: 700; color: #166534;">
                        🧺 Your Pre-Order is Packed & Ready for Pickup!
                    </p>
                    <p style="margin: 6px 0 0 0; font-size: 13px; color: #15803d; line-height: 1.5;">
                        The grower has packed your harvest basket. Head to the stall during your selected time window to collect your produce.
                    </p>
                </div>
            @elseif($status === 'accepted')
                <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 6px; padding: 16px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 16px; font-weight: 700; color: #1e40af;">
                        Pre-Order Confirmed by Grower
                    </p>
                    <p style="margin: 6px 0 0 0; font-size: 13px; color: #1d4ed8; line-height: 1.5;">
                        Your pre-order has been accepted and is locked in for preparation.
                    </p>
                </div>
            @elseif($status === 'completed')
                <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 6px; padding: 16px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 16px; font-weight: 700; color: #166534;">
                        Pre-Order Completed — Thank You!
                    </p>
                    <p style="margin: 6px 0 0 0; font-size: 13px; color: #15803d; line-height: 1.5;">
                        We hope you enjoy your farm fresh items! You can now leave a verified review for this farmer.
                    </p>
                </div>
            @else
                <div style="background-color: #f8fafc; border-left: 4px solid #64748b; border-radius: 6px; padding: 16px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 16px; font-weight: 700; color: #334155;">
                        Pre-Order Status: {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </p>
                </div>
            @endif

            <h2 style="margin: 0 0 12px 0; font-size: 18px; color: #0f172a;">Hello, {{ $order->customer?->name }}!</h2>
            <p style="margin: 0 0 20px 0; font-size: 14px; line-height: 1.6; color: #334155;">
                Here is the latest update on your pre-order <strong>#{{ $order->order_number }}</strong> with <strong>{{ $order->farmer?->stall_name }}</strong>:
            </p>

            <!-- Order Summary Box -->
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; margin-bottom: 24px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; width: 140px;">Order Number:</td>
                        <td style="padding: 6px 0; font-weight: 700; color: #0f172a;">#{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Farmer Stall:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #0f172a;">{{ $order->farmer?->stall_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Stall Location:</td>
                        <td style="padding: 6px 0; color: #0f172a;">{{ $order->farmer?->address ?? 'Main Corridor' }} ({{ $order->farmer?->market?->name ?? 'Market' }})</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Pickup Schedule:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #1b4332;">{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') : 'Market Day' }} &bull; {{ $order->pickup_time_slot ?? 'Market Hours' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Payment Status:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #0f172a;">{{ $order->payment_status === 'paid' ? 'Paid in Person' : 'Pay at Stall ($' . number_format($order->total_amount, 2) . ')' }}</td>
                    </tr>
                </table>
            </div>

            <div style="text-align: center; margin: 24px 0;">
                <a href="{{ url('/customer/orders/' . $order->id) }}" style="display: inline-block; background-color: #1b4332; color: #ffffff; padding: 12px 28px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px;">
                    View Order Details &rarr;
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f1f5f9; padding: 16px 24px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b;">
            MarketLink &bull; Farm Fresh Just a Click Away
        </div>
    </div>
</body>
</html>
