<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-Order Confirmation</title>
</head>
<body style="margin: 0; padding: 24px; background-color: #f7f9f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);">
        
        <!-- Header -->
        <div style="background-color: #1b4332; padding: 28px 32px; text-align: center;">
            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700;">MarketLink</h1>
            <p style="margin: 6px 0 0 0; color: #d8f3dc; font-size: 14px;">Pre-Order Receipt & Stall Pickup Pass</p>
        </div>

        <!-- Body -->
        <div style="padding: 32px 28px;">
            <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 6px; padding: 14px 16px; margin-bottom: 24px;">
                <p style="margin: 0; font-size: 15px; font-weight: 700; color: #166534;">
                    Pre-Order Confirmed: #{{ $order->order_number }}
                </p>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #15803d;">
                    Your harvest items are reserved. Pay directly at the stall upon pickup.
                </p>
            </div>

            <h2 style="margin: 0 0 12px 0; font-size: 18px; color: #0f172a;">Hello, {{ $order->customer?->name }}!</h2>
            <p style="margin: 0 0 20px 0; font-size: 14px; line-height: 1.6; color: #334155;">
                Thank you for supporting your local grower! Your pre-order for <strong>{{ $order->farmer?->stall_name }}</strong> has been placed.
            </p>

            <!-- Pickup Details Box -->
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; margin-bottom: 24px;">
                <h3 style="margin: 0 0 12px 0; font-size: 12px; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; font-weight: 700;">Pickup Details</h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; width: 140px;">Farm Stall:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #0f172a;">{{ $order->farmer?->stall_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Market Plaza:</td>
                        <td style="padding: 6px 0; color: #0f172a;">{{ $order->farmer?->market?->name ?? 'Local Farmers Market' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Stall Location:</td>
                        <td style="padding: 6px 0; color: #0f172a;">{{ $order->farmer?->address ?? 'Main Corridor' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Pickup Date:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #1b4332;">{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('l, F j, Y') : 'Market Day' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Time Window:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #1b4332;">{{ $order->pickup_time_slot ?? 'Regular Market Hours' }}</td>
                    </tr>
                </table>
            </div>

            <!-- Items Table -->
            <h3 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 700; color: #0f172a;">Reserved Produce</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 24px;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0; text-align: left;">
                        <th style="padding: 8px 0; color: #64748b; font-size: 12px; text-transform: uppercase;">Product</th>
                        <th style="padding: 8px 0; color: #64748b; font-size: 12px; text-transform: uppercase; text-align: center;">Qty</th>
                        <th style="padding: 8px 0; color: #64748b; font-size: 12px; text-transform: uppercase; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px 0; color: #0f172a; font-weight: 600;">{{ $item->product?->name ?? 'Produce Item' }}</td>
                        <td style="padding: 10px 0; text-align: center; color: #475569;">{{ $item->quantity }}</td>
                        <td style="padding: 10px 0; text-align: right; color: #0f172a; font-weight: 600;">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="padding: 14px 0; font-weight: 700; font-size: 15px; color: #0f172a;">Total Due at Pickup:</td>
                        <td style="padding: 14px 0; text-align: right; font-weight: 800; font-size: 18px; color: #1b4332;">${{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div style="text-align: center; margin: 24px 0;">
                <a href="{{ url('/customer/orders/' . $order->id) }}" style="display: inline-block; background-color: #1b4332; color: #ffffff; padding: 12px 28px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px;">
                    View Order in Customer Portal &rarr;
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f1f5f9; padding: 16px 24px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b;">
            MarketLink &bull; Pay at Stall Pickup &bull; 100% of proceeds support local growers
        </div>
    </div>
</body>
</html>
