<?php

declare(strict_types=1);

namespace App\Filament\Resources\Operators\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class OperatorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('country_id')
                    ->numeric(),
                TextEntry::make('name'),
                TextEntry::make('code'),
                TextEntry::make('logo_url')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                SpatieMediaLibraryImageEntry::make('logo')
                    ->collection('logo'),
            ]);
    }
}
