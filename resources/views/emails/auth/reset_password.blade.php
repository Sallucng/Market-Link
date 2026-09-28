<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your MarketLink Password</title>
</head>
<body style="margin: 0; padding: 24px; background-color: #f7f9f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; -webkit-font-smoothing: antialiased;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);">
        
        <!-- Header -->
        <div style="background-color: #1b4332; padding: 28px 32px; text-align: center;">
            <div style="display: inline-block; background-color: rgba(255, 255, 255, 0.15); width: 44px; height: 44px; line-height: 44px; border-radius: 12px; margin-bottom: 12px; font-size: 22px;">
                🔐
            </div>
            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: -0.02em;">MarketLink</h1>
            <p style="margin: 6px 0 0 0; color: #d8f3dc; font-size: 14px; font-weight: 500;">Password Reset Request</p>
        </div>

        <!-- Body Content -->
        <div style="padding: 32px 28px;">
            <h2 style="margin: 0 0 12px 0; font-size: 20px; color: #0f172a;">Hello, {{ $user->name }}!</h2>
            
            <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 1.6; color: #334155;">
                We received a request to reset the password for your MarketLink account associated with <strong>{{ $user->email }}</strong>.
            </p>

            <div style="background-color: #f0fdf4; border-left: 4px solid #22c55e; border-radius: 6px; padding: 14px 16px; margin-bottom: 24px;">
                <p style="margin: 0; font-size: 14px; color: #166534; font-weight: 600;">
                    ⏱️ Link Expiration Notice
                </p>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #15803d; line-height: 1.5;">
                    This password reset link is temporary and will expire in <strong>60 minutes</strong> for your security.
                </p>
            </div>

            <!-- Call to Action Button -->
            <div style="text-align: center; margin: 32px 0 28px 0;">
                <a href="{{ $resetUrl }}" style="display: inline-block; background-color: #1b4332; color: #ffffff; padding: 14px 36px; border-radius: 999px; font-weight: 700; text-decoration: none; font-size: 15px; box-shadow: 0 4px 12px rgba(27, 67, 50, 0.25);">
                    Reset My Password &rarr;
                </a>
            </div>

            <p style="margin: 0 0 14px 0; font-size: 13px; color: #64748b; line-height: 1.5;">
                If you are having trouble clicking the "Reset My Password" button, copy and paste the following URL into your web browser:
            </p>
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 24px; word-break: break-all;">
                <a href="{{ $resetUrl }}" style="font-size: 12px; color: #1b4332; text-decoration: underline;">{{ $resetUrl }}</a>
            </div>

            <!-- Security Callout -->
            <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 14px 16px;">
                <p style="margin: 0; font-size: 13px; color: #92400e; line-height: 1.5;">
                    🛡️ <strong>Didn't request this?</strong> If you did not ask to reset your password, you can safely ignore this email. No changes will be made to your account.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 24px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                &copy; {{ date('Y') }} MarketLink. Connecting Local Farmers with Fresh Food Lovers.
            </p>
        </div>

    </div>
</body>
</html>
