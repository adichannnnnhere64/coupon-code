<?php

declare(strict_types=1);

namespace App\Filament\Resources\CouponTransactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class CouponTransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('coupon_id')
                    ->numeric(),
                TextEntry::make('transaction_id'),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('delivery_methods')
                    ->columnSpanFull(),
                TextEntry::make('status'),
                TextEntry::make('coupon_delivered_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
