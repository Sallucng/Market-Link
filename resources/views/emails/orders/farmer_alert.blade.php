<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Pre-Order Alert</title>
</head>
<body style="margin: 0; padding: 24px; background-color: #f7f9f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);">
        
        <!-- Header -->
        <div style="background-color: #1b4332; padding: 28px 32px; text-align: center;">
            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700;">MarketLink</h1>
            <p style="margin: 6px 0 0 0; color: #d8f3dc; font-size: 14px;">Farmer Portal &bull; New Harvest Pre-Order Alert</p>
        </div>

        <!-- Body -->
        <div style="padding: 32px 28px;">
            <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 6px; padding: 14px 16px; margin-bottom: 24px;">
                <p style="margin: 0; font-size: 15px; font-weight: 700; color: #1e40af;">
                    New Pre-Order #{{ $order->order_number }}
                </p>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #1d4ed8;">
                    Scheduled Pickup: {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') : 'Market Day' }} ({{ $order->pickup_time_slot ?? 'Market Hours' }})
                </p>
            </div>

            <h2 style="margin: 0 0 12px 0; font-size: 18px; color: #0f172a;">Hello, {{ $order->farmer?->contact_person ?: $order->farmer?->stall_name }}!</h2>
            <p style="margin: 0 0 20px 0; font-size: 14px; line-height: 1.6; color: #334155;">
                You received a new pre-order from <strong>{{ $order->customer?->name }}</strong>. Please prepare and hold these items for stall pickup.
            </p>

            <!-- Customer Details Box -->
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; margin-bottom: 24px;">
                <h3 style="margin: 0 0 12px 0; font-size: 12px; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; font-weight: 700;">Customer Information</h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; width: 140px;">Customer Name:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #0f172a;">{{ $order->customer?->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Contact Email:</td>
                        <td style="padding: 6px 0; color: #0f172a;">{{ $order->customer?->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Phone:</td>
                        <td style="padding: 6px 0; color: #0f172a;">{{ $order->customer?->contact_number ?: 'N/A' }}</td>
                    </tr>
                    @if($order->notes)
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Order Notes:</td>
                        <td style="padding: 6px 0; color: #854d0e; font-style: italic;">"{{ $order->notes }}"</td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- Items Table -->
            <h3 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 700; color: #0f172a;">Items to Prepare</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 24px;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0; text-align: left;">
                        <th style="padding: 8px 0; color: #64748b; font-size: 12px; text-transform: uppercase;">Produce Item</th>
                        <th style="padding: 8px 0; color: #64748b; font-size: 12px; text-transform: uppercase; text-align: center;">Qty</th>
                        <th style="padding: 8px 0; color: #64748b; font-size: 12px; text-transform: uppercase; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px 0; color: #0f172a; font-weight: 600;">{{ $item->product?->name ?? 'Harvest Item' }}</td>
                        <td style="padding: 10px 0; text-align: center; color: #166534; font-weight: 700;">{{ $item->quantity }}</td>
                        <td style="padding: 10px 0; text-align: right; color: #0f172a; font-weight: 600;">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="padding: 14px 0; font-weight: 700; font-size: 15px; color: #0f172a;">Total to Collect at Pickup:</td>
                        <td style="padding: 14px 0; text-align: right; font-weight: 800; font-size: 18px; color: #1b4332;">${{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div style="text-align: center; margin: 24px 0;">
                <a href="{{ url('/farmer/orders/' . $order->id) }}" style="display: inline-block; background-color: #1b4332; color: #ffffff; padding: 12px 28px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px;">
                    Manage Order in Farmer Portal &rarr;
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f1f5f9; padding: 16px 24px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b;">
            MarketLink Grower Operations &bull; Keep local food networks thriving
        </div>
    </div>
</body>
</html>
