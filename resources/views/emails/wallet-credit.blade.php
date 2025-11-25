{{-- resources/views/emails/wallet-credit.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wallet Credit Notification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10B981; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .amount { font-size: 32px; font-weight: bold; color: #10B981; text-align: center; margin: 20px 0; }
        .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💰 Wallet Credited</h1>
        </div>

        <div class="content">
            <p>Hello,</p>
            <p>Your wallet has been successfully credited. You can now use this balance to purchase mobile recharge coupons.</p>

            <div class="amount">
                +{{ $amount }} {{ $currency }}
            </div>

            <div class="details">
                <h3>Transaction Details:</h3>
                <table width="100%">
                    <tr>
                        <td><strong>Amount Credited:</strong></td>
                        <td>{{ $amount }} {{ $currency }}</td>
                    </tr>
                    <tr>
                        <td><strong>New Balance:</strong></td>
                        <td><strong>{{ $newBalance }} {{ $currency }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td>{{ now()->format('M d, Y h:i A') }}</td>
                    </tr>
                </table>
            </div>

            <p>You can use this balance to purchase mobile recharge coupons for various operators and denominations.</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/coupons') }}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                    Buy Coupons Now
                </a>
            </div>

            <div class="footer">
                <p>Thank you for choosing {{ config('app.name') }}!</p>
                <p>Need help? Contact our support team at {{ config('mail.support_email', 'support@example.com') }}</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
