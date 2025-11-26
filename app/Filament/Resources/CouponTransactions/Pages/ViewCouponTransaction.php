<?php

declare(strict_types=1);

namespace App\Filament\Resources\CouponTransactions\Pages;

use App\Filament\Resources\CouponTransactions\CouponTransactionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

final class ViewCouponTransaction extends ViewRecord
{
    protected static string $resource = CouponTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
