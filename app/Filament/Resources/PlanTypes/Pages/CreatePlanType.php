<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlanTypes\Pages;

use App\Filament\Resources\PlanTypes\PlanTypeResource;
use Filament\Resources\Pages\CreateRecord;

final class CreatePlanType extends CreateRecord
{
    protected static string $resource = PlanTypeResource::class;
}
