<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to MarketLink</title>
</head>
<body style="margin: 0; padding: 24px; background-color: #f7f9f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; -webkit-font-smoothing: antialiased;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);">
        
        <!-- Header -->
        <div style="background-color: #1b4332; padding: 28px 32px; text-align: center;">
            <div style="display: inline-block; background-color: rgba(255, 255, 255, 0.15); width: 44px; height: 44px; line-height: 44px; border-radius: 12px; margin-bottom: 12px; font-size: 22px;">
                🌱
            </div>
            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: -0.02em;">MarketLink</h1>
            <p style="margin: 6px 0 0 0; color: #d8f3dc; font-size: 14px; font-weight: 500;">Farm Fresh Just a Click Away</p>
        </div>

        <!-- Body -->
        <div style="padding: 32px 28px;">
            <h2 style="margin: 0 0 12px 0; font-size: 20px; color: #0f172a;">Hello, {{ $user->name }}! 👋</h2>
            
            @if($user->isFarmer())
                <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 1.6; color: #334155;">
                    Welcome to the <strong>MarketLink Grower Network</strong>! Your farmer account has been created.
                </p>
                <div style="background-color: #f0fdf4; border-left: 4px solid #22c55e; border-radius: 6px; padding: 14px 16px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 14px; color: #166534; font-weight: 600;">
                        🌱 Stall Status: Under Rapid Admin Review
                    </p>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #15803d; line-height: 1.5;">
                        To keep local markets verified and safe, an administrator reviews newly registered stalls before produce appears in public catalogs.
                    </p>
                </div>
            @else
                <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 1.6; color: #334155;">
                    Welcome to <strong>MarketLink</strong>! Your customer account is set up and ready to go. You can now explore local community markets, meet neighborhood growers, and pre-order fresh harvest boxes for easy stall pickup.
                </p>
                <div style="background-color: #f0fdf4; border-left: 4px solid #22c55e; border-radius: 6px; padding: 14px 16px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 14px; color: #166534; font-weight: 600;">
                        🛒 Zero Online Markups &bull; Pay at Stall Pickup
                    </p>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #15803d; line-height: 1.5;">
                        Reserve your harvest ahead of time to skip long market lines, and pay the farmer in person when you pick up your fresh produce.
                    </p>
                </div>
            @endif

            <!-- Account Details Card -->
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; margin-bottom: 26px;">
                <h3 style="margin: 0 0 12px 0; font-size: 13px; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; font-weight: 700;">Account Overview</h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; width: 130px;">Name:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #0f172a;">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Username:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #0f172a;">{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Email Address:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #0f172a;">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Account Type:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #1b4332; text-transform: capitalize;">{{ $user->role }}</td>
                    </tr>
                </table>
            </div>

            <!-- Call to Action Button -->
            <div style="text-align: center; margin: 30px 0 20px 0;">
                <a href="{{ url('/') }}" style="display: inline-block; background-color: #1b4332; color: #ffffff; padding: 14px 32px; border-radius: 999px; font-weight: 700; text-decoration: none; font-size: 15px; box-shadow: 0 4px 12px rgba(27, 67, 50, 0.25);">
                    Open MarketLink Portal &rarr;
                </a>
            </div>

            <p style="margin: 24px 0 0 0; font-size: 13px; color: #64748b; line-height: 1.5; text-align: center;">
                Need help or have questions? Simply reply to this email or visit our website to reach our community support team.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f1f5f9; padding: 20px 24px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b; line-height: 1.5;">
            <p style="margin: 0 0 4px 0; font-weight: 600; color: #475569;">MarketLink — Local Farmers Market & Harvest Network</p>
            <p style="margin: 0;">You received this automated transactional email because an account was registered with this email address.</p>
        </div>
    </div>
</body>
</html>
