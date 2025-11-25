{{-- resources/views/emails/coupon-delivery.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Coupon Delivery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }

        .coupon-code {
            background: #fff;
            border: 2px dashed #4F46E5;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
        }

        .details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Your Coupon is Ready!</h1>
        </div>

        <div class="content">
            <p>Hello <strong>{{ $user->name }}</strong>,</p>
            <p>Your mobile recharge coupon has been successfully generated. Here are your coupon details:</p>

            <div class="coupon-code">
                {{ $transaction->coupon->coupon_code }}
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
                        <td><strong>Selling Price:</strong></td>
                        <td>{{ $coupon->selling_price }} {{ $coupon->operator->country->currency }}</td>
                    </tr>
                    <tr>
                        <td><strong>Validity:</strong></td>
                        <td>{{ $coupon->validity_days }} days</td>
                    </tr>
                    <tr>
                        <td><strong>Transaction ID:</strong></td>
                        <td>{{ $transaction->transaction_id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Purchase Date:</strong></td>
                        <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                </table>
            </div>

            <div class="instructions">
                <h3>How to Recharge:</h3>
                <ol>
                    <li>Dial your operator's recharge code (e.g., *121* for Airtel, *139* for Jio)</li>
                    <li>Enter the coupon code: <strong>{{ $transaction->coupon->coupon_code }}</strong></li>
                    <li>Follow the prompts to complete the recharge</li>
                    <li>You'll receive a confirmation message from your operator</li>
                </ol>
            </div>

            <p>If you face any issues while redeeming this coupon, please contact our support team immediately.</p>

            <div class="footer">
                <p>Thank you for choosing {{ config('app.name') }}!</p>
                <p>Need help? Contact our support team at {{ config('mail.support_email', 'support@example.com') }}</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>

</html>
