<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Customer Complaint</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; padding: 24px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="background-color: #0f172a; padding: 20px 24px; color: #ffffff;">
            <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: #ffffff;">MarketLink Moderation Alert</h2>
            <p style="margin: 4px 0 0 0; color: #94a3b8; font-size: 14px;">A new customer complaint requires administrative review.</p>
        </div>
        
        <div style="padding: 24px;">
            <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                <p style="margin: 0; font-size: 15px; font-weight: 600; color: #991b1b;">
                    Subject: {{ $complaint->subject }}
                </p>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #b91c1c;">
                    Type: {{ $complaint->type_label }} &bull; Status: {{ ucfirst($complaint->status) }}
                </p>
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px;">
                <tr>
                    <td style="padding: 8px 0; color: #64748b; width: 140px;">Customer:</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #0f172a;">{{ $complaint->customer?->name }} ({{ $complaint->customer?->email }})</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b;">Reported Farmer:</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #0f172a;">{{ $complaint->farmer?->stall_name }}</td>
                </tr>
                @if($complaint->order)
                <tr>
                    <td style="padding: 8px 0; color: #64748b;">Order Number:</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #0f172a;">#{{ $complaint->order->order_number }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0; color: #64748b;">Submitted At:</td>
                    <td style="padding: 8px 0; color: #0f172a;">{{ $complaint->created_at->format('M d, Y H:i A') }}</td>
                </tr>
            </table>

            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                <h4 style="margin: 0 0 8px 0; font-size: 13px; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Complaint Statement</h4>
                <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #334155; white-space: pre-line;">{{ $complaint->description }}</p>
            </div>

            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ url('/admin/complaints/' . $complaint->id) }}" style="display: inline-block; background-color: #10b981; color: #ffffff; padding: 12px 24px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px;">
                    Review Complaint in Admin Portal
                </a>
            </div>
        </div>

        <div style="background-color: #f1f5f9; padding: 16px 24px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b;">
            This is an automated notification from MarketLink Customer Moderation Engine.
        </div>
    </div>
</body>
</html>
