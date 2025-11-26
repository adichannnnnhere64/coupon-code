<?php

declare(strict_types=1);

namespace App\Filament\Resources\CouponTransactions;

use App\Filament\Resources\CouponTransactions\Pages\CreateCouponTransaction;
use App\Filament\Resources\CouponTransactions\Pages\EditCouponTransaction;
use App\Filament\Resources\CouponTransactions\Pages\ListCouponTransactions;
use App\Filament\Resources\CouponTransactions\Pages\ViewCouponTransaction;
use App\Filament\Resources\CouponTransactions\Schemas\CouponTransactionForm;
use App\Filament\Resources\CouponTransactions\Schemas\CouponTransactionInfolist;
use App\Filament\Resources\CouponTransactions\Tables\CouponTransactionsTable;
use App\Models\CouponTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

final class CouponTransactionResource extends Resource
{
    protected static ?string $model = CouponTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CouponTransactionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CouponTransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CouponTransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCouponTransactions::route('/'),
            'create' => CreateCouponTransaction::route('/create'),
            'view' => ViewCouponTransaction::route('/{record}'),
            'edit' => EditCouponTransaction::route('/{record}/edit'),
        ];
    }
}
