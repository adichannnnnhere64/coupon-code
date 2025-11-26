<?php

declare(strict_types=1);

namespace App\Filament\Resources\WalletTransactions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class WalletTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('wallet_id')
                    ->relationship('wallet', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->email)
                    ->searchable()
                    ->preload()
                    ->loadingMessage('Loading Wallets...'),
                Select::make('type')
                    ->required()
                    ->options([
                        'debit' => 'debit',
                        'credit' => 'credit',
                    ]),

                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('balance_after')
                    ->required()
                    ->numeric(),
                TextInput::make('description')
                    ->required(),
                TextInput::make('reference_id'),
                Textarea::make('metadata')
                    ->columnSpanFull(),
            ]);
    }
}
