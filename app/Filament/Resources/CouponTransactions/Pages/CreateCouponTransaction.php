<?php

declare(strict_types=1);

namespace App\Filament\Resources\CouponTransactions\Pages;

use App\Filament\Resources\CouponTransactions\CouponTransactionResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateCouponTransaction extends CreateRecord
{
    protected static string $resource = CouponTransactionResource::class;
}
