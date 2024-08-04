<?php

namespace App\Filament\Resources\AnnualStatementResource\Pages;

use App\Filament\Resources\AnnualStatementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnualStatement extends CreateRecord
{
    protected static string $resource = AnnualStatementResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
