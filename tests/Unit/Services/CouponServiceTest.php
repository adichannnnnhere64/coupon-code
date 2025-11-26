<?php

declare(strict_types=1);

use App\Contracts\Services\NotificationServiceInterface;
use App\DTOs\PurchaseCouponDTO;
use App\Exceptions\CoreException;
use App\Models\Coupon;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\Services\CouponService;

beforeEach(function (): void {
    $this->couponRepository = mock(CouponRepositoryInterface::class);
    $this->walletRepository = mock(WalletRepositoryInterface::class);
    $this->notificationService = mock(NotificationServiceInterface::class);

    $this->service = new CouponService(
        $this->couponRepository,
        $this->walletRepository,
        $this->notificationService
    );
});

it('can purchase available coupon', function (): void {
    $user = User::factory()->create();
    $pm = PaymentMethod::factory()->active()->wallet()->create();
    $coupon = Coupon::factory()->withWalletOnly()->create(['selling_price' => 100.00]);
    $wallet = Wallet::factory()->forUser($user)->create(['balance' => 200.00]);
    $dto = new PurchaseCouponDTO($user->id, $coupon->id, ['sms', 'email'], 'wallet');

    $this->couponRepository->shouldReceive('findAvailableById')
        ->with($coupon->id)
        ->andReturn($coupon);

    $this->walletRepository->shouldReceive('findByUserId')
        ->with($user->id)
        ->andReturn($wallet);

    $this->couponRepository->shouldReceive('decrementStock')
        ->with($coupon->id)
        ->andReturn(true);

    $fakeWalletTransaction = WalletTransaction::factory()->make([
        'wallet_id' => $wallet->id,
        'type' => 'debit',
        'amount' => 100.00,
    ]);

    $this->walletRepository->shouldReceive('addTransaction')
        ->once()
        ->with($wallet->id, Mockery::type('array'))
        ->andReturn($fakeWalletTransaction);

    $this->notificationService->shouldReceive('sendCouponDelivery')
        ->once();

    $transaction = $this->service->purchaseCoupon($dto);

    expect($transaction)->not->toBeNull();
    expect($transaction->user_id)->toBe($user->id);
    expect($transaction->coupon_id)->toBe($coupon->id);
    expect($transaction->status)->toBe('success');
});

it('throws exception for unavailable coupon', function (): void {
    $user = User::factory()->create();
    $dto = new PurchaseCouponDTO($user->id, 999, ['sms'], 'stripe');

    $this->couponRepository->shouldReceive('findAvailableById')
        ->with(999)
        ->andReturn(null);

    $this->service->purchaseCoupon($dto);
})->throws(CoreException::class, 'Coupon unavailable', 422);

it('throws exception for insufficient balance', function (): void {
    /* $this->withExceptionHandling(); */
    $user = User::factory()->create();
    $pm = PaymentMethod::factory()->active()->wallet()->create();
    $coupon = Coupon::factory()->withWalletOnly()->create(['selling_price' => 200.00]);
    $wallet = Wallet::factory()->forUser($user)->create(['balance' => 100.00]);
    $dto = new PurchaseCouponDTO($user->id, $coupon->id, ['sms'], 'wallet');

    $this->couponRepository->shouldReceive('findAvailableById')
        ->with($coupon->id)
        ->andReturn($coupon);

    $this->walletRepository->shouldReceive('findByUserId')
        ->with($user->id)
        ->andReturn($wallet);

    $this->service->purchaseCoupon($dto);

})->throws(CoreException::class, 'Insufficient balance', 422);

use App\Models\Country;
use App\Models\Operator;
use App\Models\PlanType;

beforeEach(function (): void {
    $this->couponService = app(CouponService::class);
    $this->country = Country::factory()->create();
    $this->operator = Operator::factory()->forCountry($this->country)->create();
    $this->planType = PlanType::factory()->create();
});

it('filters coupons by operator correctly', function (): void {
    $operator2 = Operator::factory()->forCountry($this->country)->create();

    Coupon::factory()->active()->count(3)->forOperator($this->operator)->create();
    Coupon::factory()->active()->count(2)->forOperator($operator2)->create();

    $result = $this->couponService->getFilteredCoupons(['operator_id' => $this->operator->id]);

    expect($result->total())->toBe(3);
    foreach ($result->items() as $coupon) {
        expect($coupon->operator_id)->toBe($this->operator->id);
    }
});

it('filters coupons by country correctly', function (): void {
    $usCountry = Country::factory()->create(['code' => 'USX']);
    $usOperator = Operator::factory()->forCountry($usCountry)->create();

    Coupon::factory()->count(4)->forOperator($this->operator)->create([
        'is_active' => true,
    ]);
    Coupon::factory()->count(2)->forOperator($usOperator)->create([
        'is_active' => true,
    ]);

    $result = $this->couponService->getFilteredCoupons(['country_id' => $this->country->id]);

    expect($result->total())->toBe(4);
    foreach ($result->items() as $coupon) {
        expect($coupon->operator->country_id)->toBe($this->country->id);
    }
});

it('filters coupons by price range correctly', function (): void {
    Coupon::factory()->active()->create(['selling_price' => 50.00]);
    Coupon::factory()->active()->create(['selling_price' => 150.00]);
    Coupon::factory()->active()->create(['selling_price' => 250.00]);

    $result = $this->couponService->getFilteredCoupons([
        'min_price' => 100,
        'max_price' => 200,
    ]);

    expect($result->total())->toBe(1);
    expect((float) $result->items()[0]->selling_price)->toBe(150.00);
});

it('searches coupons by operator name', function (): void {
    $specialOperator = Operator::factory()->forCountry($this->country)->create(['name' => 'SpecialTel']);
    Coupon::factory()->active()->forOperator($specialOperator)->create();
    Coupon::factory()->active()->count(2)->forOperator($this->operator)->create();

    $result = $this->couponService->getFilteredCoupons(['search' => 'Special']);

    expect($result->total())->toBe(1);
    expect($result->items()[0]->operator->name)->toBe('SpecialTel');
});

it('searches coupons by coupon code', function (): void {
    $specialCoupon = Coupon::factory()->active()->forOperator($this->operator)->create(['coupon_code' => 'UNIQUE123']);
    Coupon::factory()->count(2)->forOperator($this->operator)->create();

    $result = $this->couponService->getFilteredCoupons(['search' => 'UNIQUE']);

    expect($result->total())->toBe(1);
    expect($result->items()[0]->coupon_code)->toBe('UNIQUE123');
});

it('sorts coupons by denomination ascending', function (): void {
    Coupon::factory()->create(['denomination' => 500.00, 'is_active' => true]);
    Coupon::factory()->create(['denomination' => 100.00, 'is_active' => true]);
    Coupon::factory()->create(['denomination' => 200.00, 'is_active' => true]);

    $result = $this->couponService->getFilteredCoupons([
        'sort_by' => 'denomination',
        'sort_order' => 'asc',
    ]);

    $denominations = array_map(fn ($coupon) => $coupon->denomination, $result->items());
    expect($denominations)->toEqual([100.00, 200.00, 500.00]);
});

it('sorts coupons by selling price descending', function (): void {
    Coupon::factory()->create(['selling_price' => 50.00, 'is_active' => true]);
    Coupon::factory()->create(['selling_price' => 150.00, 'is_active' => true]);
    Coupon::factory()->create(['selling_price' => 100.00, 'is_active' => true]);

    $result = $this->couponService->getFilteredCoupons([
        'sort_by' => 'selling_price',
        'sort_order' => 'desc',
    ]);

    $prices = array_map(fn ($coupon) => $coupon->selling_price, $result->items());
    expect($prices)->toEqual([150.00, 100.00, 50.00]);
});

it('only returns active and in-stock coupons', function (): void {
    Coupon::factory()->inStock()->active()->create(); // Should be included
    Coupon::factory()->outOfStock()->active()->create(); // Should be excluded
    Coupon::factory()->inStock()->inactive()->create(); // Should be excluded

    $result = $this->couponService->getFilteredCoupons();

    expect($result->total())->toBe(1);
    expect($result->items()[0]->is_active)->toBeTrue();
    expect($result->items()[0]->stock_quantity)->toBeGreaterThan(0);
});

it('handles pagination correctly', function (): void {
    Coupon::query()->delete();
    Coupon::factory()->count(25)->forOperator($this->operator)->create([
        'is_active' => true,
    ]);

    $result = $this->couponService->getFilteredCoupons(['per_page' => 10]);

    expect($result->total())->toBe(25);
    expect($result->perPage())->toBe(10);
    expect($result->count())->toBe(10); // Current page items
});
