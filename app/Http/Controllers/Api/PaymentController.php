<?php

declare(strict_types=1);

// app/Http/Controllers/Api/PaymentController.php

namespace App\Http\Controllers\Api;

use App\DTOs\PaymentIntentDTO;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function createPayment(Request $request)
    {
        $paymentIntent = PaymentIntentDTO::fromArray([
            'amount' => $request->amount,
            'currency' => $request->currency ?? 'USD',
            'description' => $request->description,
            'metadata' => [
                'user_id' => auth()->id(),
                'wallet_topup' => true,
            ],
            'customer_email' => auth()->user()->email,
            'return_url' => route('payment.success'),
            'cancel_url' => route('payment.cancel'),
        ]);

        $payment = $this->paymentService->createPayment($paymentIntent);

        return response()->json([
            'gateway' => $this->paymentService->getGateway()->getName(),
            'payment' => $payment,
        ]);
    }

    public function confirmPayment(string $paymentIntentId)
    {
        $confirmation = $this->paymentService->confirmPayment($paymentIntentId);

        if ($confirmation->status === 'succeeded') {
            // Update wallet balance
            // Create transaction record
            return response()->json([
                'message' => 'Payment successful',
                'payment' => $confirmation,
            ]);
        }

        return response()->json([
            'message' => 'Payment failed',
            'error' => $confirmation->errorMessage,
        ], 422);
    }
}
