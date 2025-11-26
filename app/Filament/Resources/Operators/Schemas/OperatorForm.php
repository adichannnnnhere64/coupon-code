<?php

declare(strict_types=1);

namespace App\Filament\Resources\Operators\Schemas;

use App\Filament\Resources\Countries\Schemas\CountryForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

final class OperatorForm
{
    public static function form(): array
    {
        return [
            Select::make('country_id')
                ->relationship(name: 'country', titleAttribute: 'name')
                ->createOptionForm(CountryForm::form())
                ->searchable()

                ->preload()
                ->loadingMessage('Loading operators...'),
            TextInput::make('name')
                ->required(),
            TextInput::make('code')
                ->required(),
            /* TextInput::make('logo_url') */
            /*     ->url(), */
            SpatieMediaLibraryFileUpload::make('logo')
                ->collection('logo'),
            Toggle::make('is_active')
                ->required(),
        ];

    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::form());
    }
}
