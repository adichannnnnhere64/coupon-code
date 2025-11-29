<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\NotificationServiceInterface;
use App\DTOs\PaymentIntentDTO;
use App\DTOs\PurchaseCouponDTO;
use App\Exceptions\CustomException;
use App\Models\Coupon;
use App\Models\CouponTransaction;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\ValueObjects\Money;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class CouponService
{
    public function __construct(
        private CouponRepositoryInterface $couponRepository,
        private WalletRepositoryInterface $walletRepository,
        private NotificationServiceInterface $notificationService
    ) {}

    public function getFilteredCoupons(array $filters = []): LengthAwarePaginator
    {
        $query = Coupon::query()
            ->with(['operator.country', 'planType'])
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);

        // Apply filters
        $this->applyFilters($query, $filters);

        // Apply sorting
        $this->applySorting($query, $filters);

        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    public function getCoupon(int $id)
    {
        return Coupon::query()
            ->where('is_active', true)
            ->where('id', $id)->firstOrFail();
    }

    public function getAvailableCoupons(int $operatorId, int $planTypeId)
    {
        return $this->couponRepository->findAvailableCoupons($operatorId, $planTypeId);
    }

    public function purchaseCoupon(PurchaseCouponDTO $dto): CouponTransaction
    {
        // Find and validate coupon
        $coupon = $this->couponRepository->findAvailableById($dto->couponId);
        throw_unless($coupon, CustomException::couponUnavailable());

        // Check if coupon supports the selected payment method
        /* throw_unless($coupon->supportsPaymentMethod($dto->paymentMethod), Exception::class, 'Selected payment method is not supported for this coupon'); */

        // Handle wallet payment separately
        if ($dto->paymentMethod === 'wallet') {
            return $this->processWalletPayment($coupon, $dto);
        }

        // Handle gateway payments (Stripe, PayPal)
        return $this->processGatewayPayment($coupon, $dto);
    }

    private function applyFilters($query, array $filters): void
    {
        // Search by operator name or coupon code
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters): void {
                $q->where('coupon_code', 'like', "%{$filters['search']}%")
                    ->orWhereHas('operator', function ($operatorQuery) use ($filters): void {
                        $operatorQuery->where('name', 'like', "%{$filters['search']}%");
                    });
            });
        }

        // Filter by operator
        if (! empty($filters['operator_id'])) {
            $query->where('operator_id', $filters['operator_id']);
        }

        // Filter by country (through operator)
        if (! empty($filters['country_id'])) {
            $query->whereHas('operator', function ($operatorQuery) use ($filters): void {
                $operatorQuery->where('country_id', $filters['country_id']);
            });
        }

        // Filter by plan type
        if (! empty($filters['plan_type_id'])) {
            $query->where('plan_type_id', $filters['plan_type_id']);
        }

        // Filter by price range
        if (! empty($filters['min_price'])) {
            $query->where('selling_price', '>=', $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('selling_price', '<=', $filters['max_price']);
        }

        // Filter by denomination
        if (! empty($filters['denomination'])) {
            $query->where('denomination', $filters['denomination']);
        }

        // Filter by stock availability
        if (isset($filters['in_stock']) && $filters['in_stock']) {
            $query->where('stock_quantity', '>', 0);
        }
    }

    private function applySorting($query, array $filters): void
    {
        $sortBy = $filters['sort_by'] ?? 'denomination';
        $sortOrder = $filters['sort_order'] ?? 'asc';

        match ($sortBy) {
            'selling_price' => $query->orderBy('selling_price', $sortOrder),
            'created_at' => $query->orderBy('created_at', $sortOrder),
            // You might want to order by purchase count or some other metric
            'popularity' => $query->orderBy('created_at', 'desc'),
            // denomination
            default => $query->orderBy('denomination', $sortOrder),
        };
    }

    private function processWalletPayment(Coupon $coupon, PurchaseCouponDTO $dto): CouponTransaction
    {
        $wallet = $this->walletRepository->findByUserId($dto->userId);
        $amount = new Money((float) $coupon->selling_price);

        throw_unless($wallet->hasSufficientBalance($amount), CustomException::insufficientBalance());

        return DB::transaction(function () use ($wallet, $coupon, $dto, $amount) {
            // Deduct from wallet
            $wallet->deductAmount($amount);

            // Decrement coupon stock
            $this->couponRepository->decrementStock($coupon->id);

            // Create coupon transaction record
            $transaction = CouponTransaction::query()->create([
                'user_id' => $dto->userId,
                'coupon_id' => $coupon->id,
                'transaction_id' => 'WLT-'.Str::upper(Str::random(12)),
                'amount' => $coupon->selling_price,
                /* 'delivery_methods' => json_encode($dto->deliveryMethods), */
                'status' => 'success',
                'coupon_delivered_at' => now(),
                'payment_method' => 'wallet',
            ]);

            // Record wallet transaction
            $this->walletRepository->addTransaction($wallet->id, [
                'type' => 'debit',
                'amount' => $amount->getAmount(),
                'balance_after' => $wallet->balance,
                'description' => "Coupon purchase: {$coupon->operator->name} - {$coupon->denomination}",
                'reference_id' => $transaction->transaction_id,
            ]);

            // Send notifications
            /* $this->notificationService->sendCouponDelivery($transaction, $dto->deliveryMethods); */
            $this->notificationService->sendCouponDelivery($transaction);

            return $transaction;
        });
    }

    private function processGatewayPayment(Coupon $coupon, PurchaseCouponDTO $dto): CouponTransaction
    {
        // Use the payment service for gateway payments
        $paymentService = app(PaymentService::class);
        $paymentService->setGateway($dto->paymentMethod);

        $paymentIntent = new PaymentIntentDTO(
            amount: (float) $coupon->selling_price,
            currency: 'USD', // or get from coupon/operator/country
            description: "Coupon: {$coupon->operator->name} - {$coupon->denomination}",
            metadata: [
                'user_id' => $dto->userId,
                'coupon_id' => $coupon->id,
                /* 'delivery_methods' => $dto->deliveryMethods, */
            ]
        );

        $payment = $paymentService->createPayment($paymentIntent);

        // Create pending transaction
        $transaction = CouponTransaction::query()->create([
            'user_id' => $dto->userId,
            'coupon_id' => $coupon->id,
            'transaction_id' => $payment['payment_intent_id'],
            'amount' => $coupon->selling_price,
            /* 'delivery_methods' => json_encode($dto->deliveryMethods), */
            'status' => 'pending',
            'payment_method' => $dto->paymentMethod,
            'payment_data' => $payment, // Store gateway response
        ]);

        return $transaction;
    }
}
