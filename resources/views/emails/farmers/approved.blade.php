<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stall Approved</title>
</head>
<body style="margin: 0; padding: 24px; background-color: #f7f9f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);">
        
        <!-- Header -->
        <div style="background-color: #1b4332; padding: 28px 32px; text-align: center;">
            <div style="display: inline-block; background-color: rgba(255, 255, 255, 0.15); width: 44px; height: 44px; line-height: 44px; border-radius: 12px; margin-bottom: 12px; font-size: 22px;">
                🎉
            </div>
            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700;">MarketLink</h1>
            <p style="margin: 6px 0 0 0; color: #d8f3dc; font-size: 14px;">Farmer Verification & Approval</p>
        </div>

        <!-- Body -->
        <div style="padding: 32px 28px;">
            <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 6px; padding: 16px; margin-bottom: 24px;">
                <p style="margin: 0; font-size: 16px; font-weight: 700; color: #166534;">
                    🎉 Congratulations! Your Farm Stall is Approved
                </p>
                <p style="margin: 6px 0 0 0; font-size: 13px; color: #15803d; line-height: 1.5;">
                    Your stall profile and farm products are now publicly visible to community shoppers across MarketLink.
                </p>
            </div>

            <h2 style="margin: 0 0 12px 0; font-size: 18px; color: #0f172a;">Hello, {{ $farmer->contact_person ?: $farmer->stall_name }}!</h2>
            <p style="margin: 0 0 20px 0; font-size: 14px; line-height: 1.6; color: #334155;">
                We are thrilled to welcome <strong>{{ $farmer->stall_name }}</strong> to the verified MarketLink producer family. Our admin team has reviewed your stall registration and approved your seller profile.
            </p>

            <!-- What You Can Do Now -->
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; margin-bottom: 24px;">
                <h3 style="margin: 0 0 12px 0; font-size: 12px; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; font-weight: 700;">Next Steps for Your Stall</h3>
                <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #334155; line-height: 1.8;">
                    <li><strong>List Fresh Produce:</strong> Add your weekly harvest items, photos, and quantities in the catalog.</li>
                    <li><strong>Set Cutoff Hours:</strong> Configure your preparation cutoff hours so harvest schedules stay calm.</li>
                    <li><strong>Receive Pre-Orders:</strong> Shoppers can now reserve items and pick them up at your stall.</li>
                    <li><strong>Chat with Shoppers:</strong> Answer questions and build direct relationships with local patrons.</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 30px 0 20px 0;">
                <a href="{{ url('/farmer/dashboard') }}" style="display: inline-block; background-color: #1b4332; color: #ffffff; padding: 14px 32px; border-radius: 999px; font-weight: 700; text-decoration: none; font-size: 15px;">
                    Open Farmer Portal &rarr;
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f1f5f9; padding: 16px 24px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b;">
            MarketLink Grower Network &bull; Direct Farm-to-Consumer Food Systems
        </div>
    </div>
</body>
</html>
