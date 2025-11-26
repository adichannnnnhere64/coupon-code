<?php

declare(strict_types=1);

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

final class CountryForm
{
    public static function form(): array
    {
        return [
            TextInput::make('name')
                ->required(),
            TextInput::make('code')
                ->required(),
            TextInput::make('currency')
                ->required(),
            Toggle::make('is_active')
                ->required(),
            SpatieMediaLibraryFileUpload::make('flag')
                ->collection('flag'),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::form());
    }
}
