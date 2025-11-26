<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wallets\Pages;

use App\Filament\Resources\Wallets\WalletResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateWallet extends CreateRecord
{
    protected static string $resource = WalletResource::class;
}
