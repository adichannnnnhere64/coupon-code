<?php

declare(strict_types=1);

namespace App\Filament\Resources\Operators\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

final class OperatorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('country_id')
                    ->relationship(name: 'country', titleAttribute: 'name')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('code')
                            ->required(),
                        TextInput::make('currency')
                            ->required(),
                    ])
                    ->searchable()

                    ->preload()
                    /* ->getSearchResultsUsing(function (string $search) { */
                    /*     return \App\Models\Country::query() */
                    /*         ->where('name', 'like', "%{$search}%") */
                    /*         ->limit(5) */
                    /*         ->pluck('name', 'id'); */
                    /* }) */
                    ->loadingMessage('Loading authors...'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('logo_url')
                    ->url(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
