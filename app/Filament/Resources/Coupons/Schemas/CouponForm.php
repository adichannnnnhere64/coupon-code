<?php

declare(strict_types=1);

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

final class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('operator_id')
                    ->required()
                    ->numeric(),
                TextInput::make('plan_type_id')
                    ->required()
                    ->numeric(),
                TextInput::make('denomination')
                    ->required()
                    ->numeric(),
                TextInput::make('selling_price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('coupon_code')
                    ->required(),
                TextInput::make('serial_number')
                    ->required(),
                TextInput::make('validity_days')
                    ->numeric(),
                TextInput::make('stock_quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('low_stock_threshold')
                    ->required()
                    ->numeric()
                    ->default(10),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
