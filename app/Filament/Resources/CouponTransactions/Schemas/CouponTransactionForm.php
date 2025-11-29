<?php

declare(strict_types=1);

namespace App\Filament\Resources\CouponTransactions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class CouponTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship(name: 'user', titleAttribute: 'email')
                    ->searchable()
                    ->preload()
                    ->loadingMessage('Loading users...'),
                Select::make('coupon_id')
                    ->relationship(name: 'coupon', titleAttribute: 'coupon_code')
                    ->searchable()
                    ->preload()
                    ->loadingMessage('Loading coupons...'),
                TextInput::make('transaction_id')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                /* Textarea::make('delivery_methods') */
                /*     ->required() */
                /*     ->columnSpanFull(), */
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                DateTimePicker::make('coupon_delivered_at'),
            ]);
    }
}
