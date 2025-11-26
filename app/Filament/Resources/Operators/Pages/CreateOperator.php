<?php

declare(strict_types=1);

namespace App\Filament\Resources\Operators\Pages;

use App\Filament\Resources\Operators\OperatorResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateOperator extends CreateRecord
{
    protected static string $resource = OperatorResource::class;
}
