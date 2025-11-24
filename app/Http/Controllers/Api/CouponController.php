<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTOs\PurchaseCouponDTO;
use App\Http\Requests\PurchaseCouponRequest;
use App\Http\Resources\CouponResource;
use App\Http\Resources\CouponTransactionResource;
use App\Services\CouponService;
use Illuminate\Routing\Controller;

final class CouponController extends Controller
{
    public function __construct(private readonly CouponService $couponService) {}

    public function purchase(PurchaseCouponRequest $request): CouponTransactionResource
    {
        $dto = PurchaseCouponDTO::fromRequest($request->validated());

        $transaction = $this->couponService->purchaseCoupon($dto);

        return new CouponTransactionResource($transaction);
    }

    public function available(int $operatorId, int $planTypeId)
    {
        $coupons = $this->couponService->getAvailableCoupons($operatorId, $planTypeId);

        return CouponResource::collection($coupons);
    }
}
