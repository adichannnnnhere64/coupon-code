{{-- resources/views/emails/low-stock-alert.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Low Stock Alert</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #F59E0B; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .alert { background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 15px; margin: 20px 0; }
        .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Low Stock Alert</h1>
        </div>

        <div class="content">
            <p>Hello Admin,</p>

            <div class="alert">
                <h3 style="margin: 0; color: #92400E;">Attention Required!</h3>
                <p style="margin: 10px 0 0 0;">The following coupon is running low on stock and needs to be replenished soon.</p>
            </div>

            <div class="details">
                <h3>Coupon Details:</h3>
                <table width="100%">
                    <tr>
                        <td><strong>Operator:</strong></td>
                        <td>{{ $coupon->operator->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Plan Type:</strong></td>
                        <td>{{ $coupon->planType->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Denomination:</strong></td>
                        <td>{{ $coupon->denomination }} {{ $coupon->operator->country->currency }}</td>
                    </tr>
                    <tr>
                        <td><strong>Current Stock:</strong></td>
                        <td style="color: #DC2626; font-weight: bold;">{{ $coupon->stock_quantity }}</td>
                    </tr>
                    <tr>
                        <td><strong>Low Stock Threshold:</strong></td>
                        <td>{{ $coupon->low_stock_threshold }}</td>
                    </tr>
                    <tr>
                        <td><strong>Coupon Code:</strong></td>
                        <td>{{ $coupon->coupon_code }}</td>
                    </tr>
                </table>
            </div>

            <p>Please take necessary action to replenish the stock to avoid service disruption.</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/admin/coupons/' . $coupon->id . '/edit') }}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                    Manage Coupon Stock
                </a>
            </div>

            <div class="footer">
                <p>This is an automated alert from {{ config('app.name') }}</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
