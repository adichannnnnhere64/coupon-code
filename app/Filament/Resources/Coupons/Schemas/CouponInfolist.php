<?php

declare(strict_types=1);

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class CouponInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SpatieMediaLibraryImageEntry::make('image')
                    ->collection('images'),
                TextEntry::make('operator_id')
                    ->numeric(),
                TextEntry::make('plan_type_id')
                    ->numeric(),
                TextEntry::make('denomination')
                    ->numeric(),
                TextEntry::make('selling_price')
                    ->money(),
                TextEntry::make('coupon_code'),
                TextEntry::make('serial_number'),
                TextEntry::make('validity_days')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('stock_quantity')
                    ->numeric(),
                TextEntry::make('low_stock_threshold')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
