<?php

declare(strict_types=1);

namespace App\Filament\Resources\CouponTransactions\Pages;

use App\Filament\Resources\CouponTransactions\CouponTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

final class EditCouponTransaction extends EditRecord
{
    protected static string $resource = CouponTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
