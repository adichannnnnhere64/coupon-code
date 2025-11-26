<?php

declare(strict_types=1);

// app/Http/Controllers/Api/MediaController.php

namespace App\Http\Controllers\Api;

use App\Models\Coupon;
use App\Services\PaymentService;
use Illuminate\Routing\Controller;

final class PaymentMethodController extends Controller
{
    public function getPaymentMethods($couponId)
    {
        $coupon = Coupon::query()->findOrFail($couponId);
        $paymentService = app(PaymentService::class);

        $availableMethods = $paymentService->getAvailablePaymentMethodsForCoupon(
            $coupon,
            auth()->id()
        );

        return response()->json([
            'coupon_id' => $coupon->id,
            'coupon_price' => (float) $coupon->selling_price,
            'available_payment_methods' => $availableMethods,
        ]);
    }
}
