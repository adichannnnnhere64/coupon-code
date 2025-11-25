<?php

declare(strict_types=1);

use App\Contracts\Services\NotificationServiceInterface;
use App\DTOs\PurchaseCouponDTO;
use App\Exceptions\CoreException;
use App\Models\Coupon;
use App\Models\User;
use App\Models\Wallet;
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
    $coupon = Coupon::factory()->create(['selling_price' => 100.00]);
    $wallet = Wallet::factory()->forUser($user)->create(['balance' => 200.00]);
    $dto = new PurchaseCouponDTO($user->id, $coupon->id, ['sms', 'email']);

    $this->couponRepository->shouldReceive('findAvailableById')
        ->with($coupon->id)
        ->andReturn($coupon);

    $this->walletRepository->shouldReceive('findByUserId')
        ->with($user->id)
        ->andReturn($wallet);

    $this->couponRepository->shouldReceive('decrementStock')
        ->with($coupon->id)
        ->andReturn(true);

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
    $dto = new PurchaseCouponDTO($user->id, 999, ['sms']);

    $this->couponRepository->shouldReceive('findAvailableById')
        ->with(999)
        ->andReturn(null);

    $this->service->purchaseCoupon($dto);
})->throws(CoreException::class, 'Coupon unavailable', 422);

it('throws exception for insufficient balance', function (): void {
    /* $this->withExceptionHandling(); */
    $user = User::factory()->create();
    $coupon = Coupon::factory()->create(['selling_price' => 200.00]);
    $wallet = Wallet::factory()->forUser($user)->create(['balance' => 100.00]);
    $dto = new PurchaseCouponDTO($user->id, $coupon->id, ['sms']);

    $this->couponRepository->shouldReceive('findAvailableById')
        ->with($coupon->id)
        ->andReturn($coupon);

    $this->walletRepository->shouldReceive('findByUserId')
        ->with($user->id)
        ->andReturn($wallet);

    $this->service->purchaseCoupon($dto);

})->throws(CoreException::class, 'Insufficient balance', 422);
