<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $order->order_number }} — MarketLink</title>
    <style>
        @page {
            margin: 25px 30px;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.45;
            color: #2b2d42;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #2d6a4f;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .brand-title {
            font-size: 26px;
            font-weight: bold;
            color: #1b4332;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .brand-subtitle {
            font-size: 11px;
            color: #52b788;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .doc-title {
            text-align: right;
        }
        .receipt-badge {
            font-size: 18px;
            font-weight: bold;
            color: #1b4332;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .order-meta {
            font-size: 11px;
            color: #555;
            margin-top: 4px;
        }
        
        /* Status Banner */
        .status-box {
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-width: 1px;
            border-style: solid;
        }
        .status-paid {
            background-color: #d8f3dc;
            border-color: #74c69d;
            color: #1b4332;
        }
        .status-pending {
            background-color: #fefae0;
            border-color: #dda15e;
            color: #604000;
        }
        .status-title {
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .status-desc {
            font-size: 11px;
        }

        /* 2-column info layout */
        .info-table {
            width: 100%;
            margin-bottom: 22px;
            border-collapse: separate;
            border-spacing: 12px 0;
        }
        .info-cell {
            vertical-align: top;
            width: 50%;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 12px 14px;
        }
        .info-heading {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #2d6a4f;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .info-line {
            font-size: 11px;
            margin-bottom: 4px;
            color: #333;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            width: 85px;
        }

        /* Pickup Schedule Card */
        .pickup-table {
            width: 100%;
            background-color: #e8f5e9;
            border: 1px solid #b7e4c7;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 22px;
        }
        .pickup-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1b4332;
            margin-bottom: 4px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #2d6a4f;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #2d6a4f;
        }
        .items-table th.text-center { text-align: center; }
        .items-table th.text-right { text-align: right; }
        .items-table td {
            padding: 9px 10px;
            border-bottom: 1px solid #e9ecef;
            border-left: 1px solid #e9ecef;
            border-right: 1px solid #e9ecef;
            font-size: 11px;
        }
        .items-table tr:nth-child(even) td {
            background-color: #fafbfa;
        }
        .items-table td.text-center { text-align: center; }
        .items-table td.text-right { text-align: right; }

        /* Summary / Totals Table */
        .summary-wrapper {
            width: 100%;
            margin-bottom: 25px;
        }
        .summary-table {
            width: 280px;
            float: right;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 5px 8px;
            font-size: 11px;
        }
        .summary-table .label {
            text-align: right;
            color: #495057;
        }
        .summary-table .amount {
            text-align: right;
            font-weight: 600;
            color: #212529;
            width: 90px;
        }
        .summary-table .total-row td {
            border-top: 2px solid #2d6a4f;
            border-bottom: 2px solid #2d6a4f;
            font-size: 14px;
            font-weight: bold;
            color: #1b4332;
            padding: 8px 8px;
            background-color: #f1f8f4;
        }
        .summary-table .settlement-row td {
            font-size: 12px;
            font-weight: bold;
            padding: 6px 8px;
        }

        .clear {
            clear: both;
        }

        /* Instructions & Footer */
        .notice-box {
            background-color: #f8f9fa;
            border-left: 3px solid #52b788;
            padding: 10px 14px;
            font-size: 10.5px;
            color: #495057;
            margin-top: 15px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }
        .notice-title {
            font-weight: bold;
            color: #1b4332;
            margin-bottom: 3px;
        }

        .footer {
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            text-align: center;
            font-size: 9.5px;
            color: #888;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align: top;">
                <div class="brand-title">MarketLink</div>
                <div class="brand-subtitle">Local Farmers Market Pre-Order Network</div>
                <div style="font-size: 10px; color: #666; margin-top: 3px;">Fresh Harvests directly from Local Growers</div>
            </td>
            <td class="doc-title" style="vertical-align: top;">
                <div class="receipt-badge">{{ $order->isPaid() ? 'Official Receipt' : 'Order Voucher' }}</div>
                <div class="order-meta">
                    <strong>Order #:</strong> {{ $order->order_number }}<br>
                    <strong>Date Placed:</strong> {{ $order->created_at->format('M d, Y - h:i A') }}<br>
                    <strong>Receipt Generated:</strong> {{ now()->format('M d, Y') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Status Banner -->
    @if($order->isPaid())
        <div class="status-box status-paid">
            <table width="100%">
                <tr>
                    <td style="vertical-align: middle;">
                        <div class="status-title">&#10003; Payment Settled & Completed</div>
                        <div class="status-desc">This pre-order has been paid in full and collected at the vendor stall. Thank you for your purchase!</div>
                    </td>
                    <td style="text-align: right; vertical-align: middle; width: 140px;">
                        <strong style="color: #1b4332; font-size: 14px; border: 2px solid #2d6a4f; padding: 4px 10px; border-radius: 4px; display: inline-block;">PAID</strong>
                    </td>
                </tr>
            </table>
        </div>
    @elseif($order->order_status === 'cancelled')
        <div class="status-box" style="background-color: #ffebee; border-color: #ef9a9a; color: #c62828;">
            <div class="status-title">Order Cancelled</div>
            <div class="status-desc">This pre-order reservation was cancelled prior to the fulfillment cutoff.</div>
        </div>
    @elseif($order->order_status === 'declined')
        <div class="status-box" style="background-color: #f5f5f5; border-color: #bdbdbd; color: #424242;">
            <div class="status-title">Order Declined</div>
            <div class="status-desc">This pre-order reservation was declined by the grower due to unexpected stock unavailability.</div>
        </div>
    @else
        <div class="status-box status-pending">
            <table width="100%">
                <tr>
                    <td style="vertical-align: middle;">
                        <div class="status-title">&#9200; Pre-Order Confirmed — Payment Due at Pickup</div>
                        <div class="status-desc">Your produce is reserved. Settle ${{ number_format($order->total_amount, 2) }} directly with the farmer in cash or card upon collection.</div>
                    </td>
                    <td style="text-align: right; vertical-align: middle; width: 140px;">
                        <strong style="color: #855300; font-size: 12px; border: 2px solid #b57a1e; padding: 4px 8px; border-radius: 4px; display: inline-block;">DUE AT PICKUP</strong>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <!-- Parties Info (Customer & Farmer) -->
    <table class="info-table" cellpadding="0" cellspacing="0">
        <tr>
            <!-- Customer Details -->
            <td class="info-cell">
                <div class="info-heading">Customer Information</div>
                <div class="info-line"><span class="info-label">Name:</span> {{ $order->customer->name ?? 'Customer' }}</div>
                <div class="info-line"><span class="info-label">Email:</span> {{ $order->customer->email ?? 'N/A' }}</div>
                <div class="info-line"><span class="info-label">Contact:</span> {{ $order->customer->contact_number ?? 'N/A' }}</div>
                @if($order->customer->address)
                    <div class="info-line"><span class="info-label">Address:</span> {{ $order->customer->address }}</div>
                @endif
            </td>
            <!-- Farmer / Stall Details -->
            <td class="info-cell">
                <div class="info-heading">Vendor & Stall Information</div>
                <div class="info-line"><span class="info-label">Stall Name:</span> <strong>{{ $order->farmer->stall_name ?? 'Farm Stall' }}</strong></div>
                <div class="info-line"><span class="info-label">Producer:</span> {{ $order->farmer->contact_person ?? ($order->farmer->user->name ?? 'Local Farmer') }}</div>
                <div class="info-line"><span class="info-label">Contact:</span> {{ $order->farmer->contact_number ?? ($order->farmer->user->contact_number ?? 'N/A') }}</div>
                <div class="info-line"><span class="info-label">Market Plaza:</span> {{ $order->farmer->market->name ?? 'Community Farmers Market' }}</div>
                <div class="info-line"><span class="info-label">Location:</span> {{ $order->farmer->market->address ?? $order->farmer->address }}, {{ $order->farmer->market->city ?? '' }}</div>
            </td>
        </tr>
    </table>

    <!-- Pickup Schedule -->
    <table class="pickup-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="pickup-title">Scheduled Pickup Date</div>
                <div style="font-size: 12px; font-weight: bold; color: #1b4332;">{{ $order->pickup_date->format('l, F j, Y') }}</div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="pickup-title">Designated Pickup Window</div>
                <div style="font-size: 12px; font-weight: bold; color: #1b4332;">{{ $order->pickup_time_slot }}</div>
            </td>
        </tr>
        @if($order->notes)
            <tr>
                <td colspan="2" style="padding-top: 8px; font-size: 11px; color: #333; border-top: 1px dashed #b7e4c7; margin-top: 6px;">
                    <strong>Customer Instructions / Special Request:</strong> {{ $order->notes }}
                </td>
            </tr>
        @endif
    </table>

    <!-- Itemized Produce Table -->
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">#</th>
                <th>Harvest Item & Description</th>
                <th style="width: 80px;" class="text-center">Unit / Measure</th>
                <th style="width: 50px;" class="text-center">Qty</th>
                <th style="width: 90px;" class="text-right">Unit Price</th>
                <th style="width: 95px;" class="text-right">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td class="text-center" style="color: #777;">{{ $index + 1 }}</td>
                    <td>
                        <strong style="color: #1b4332;">{{ $item->product->name ?? 'Fresh Produce' }}</strong>
                        @if($item->product && $item->product->category)
                            <span style="color: #666; font-size: 10px;">({{ $item->product->category->name }})</span>
                        @endif
                    </td>
                    <td class="text-center" style="color: #555;">{{ $item->product->unit ?? 'unit' }}</td>
                    <td class="text-center"><strong>{{ $item->quantity }}</strong></td>
                    <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right"><strong>${{ number_format($item->subtotal, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Summary Table -->
    <div class="summary-wrapper">
        <table class="summary-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Produce Subtotal:</td>
                <td class="amount">${{ number_format($order->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Platform / Booking Fee:</td>
                <td class="amount">$0.00</td>
            </tr>
            <tr class="total-row">
                <td class="label" style="color: #1b4332;">Total Order Value:</td>
                <td class="amount" style="color: #1b4332;">${{ number_format($order->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Payment Method:</td>
                <td class="amount" style="font-size: 10px; text-transform: uppercase;">Pay at Pickup</td>
            </tr>
            @if($order->isPaid())
                <tr class="settlement-row" style="color: #2d6a4f;">
                    <td class="label" style="color: #2d6a4f;">Amount Paid & Settled:</td>
                    <td class="amount" style="color: #2d6a4f;">${{ number_format($order->total_amount, 2) }}</td>
                </tr>
                <tr class="settlement-row">
                    <td class="label" style="color: #666;">Balance Remaining:</td>
                    <td class="amount" style="color: #666;">$0.00</td>
                </tr>
            @else
                <tr class="settlement-row" style="color: #c05621;">
                    <td class="label" style="color: #c05621;">Amount Due at Stall:</td>
                    <td class="amount" style="color: #c05621;">${{ number_format($order->total_amount, 2) }}</td>
                </tr>
            @endif
        </table>
        <div class="clear"></div>
    </div>

    <!-- Instructions / Pickup Voucher Notice -->
    <div class="notice-box">
        <div class="notice-title">Pickup & Fulfillment Instructions:</div>
        Please present this receipt (printed or on your mobile device) or reference Order <strong>#{{ $order->order_number }}</strong> when you visit <strong>{{ $order->farmer->stall_name }}</strong> stall at <strong>{{ $order->farmer->market->name ?? 'the Farmers Market' }}</strong> on <strong>{{ $order->pickup_date->format('M d, Y') }}</strong> during <strong>{{ $order->pickup_time_slot }}</strong>.
        @if(!$order->isPaid())
            Payment will be collected at the stall counter via cash or card.
        @endif
    </div>

    <!-- Official Footer -->
    <div class="footer">
        <div>MarketLink Local Produce Direct Pre-Order Network &bull; SRS v1.0 Compliant</div>
        <div>Thank you for choosing local, sustainable farming and strengthening our community food sheds.</div>
        <div style="margin-top: 4px; font-size: 8.5px; color: #aaa;">This is a system-generated document. For customer support or order inquiries, contact support@marketlink.local</div>
    </div>

</body>
</html>
