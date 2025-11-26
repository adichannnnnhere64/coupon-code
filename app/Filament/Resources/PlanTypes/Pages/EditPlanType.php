<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlanTypes\Pages;

use App\Filament\Resources\PlanTypes\PlanTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

final class EditPlanType extends EditRecord
{
    protected static string $resource = PlanTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
