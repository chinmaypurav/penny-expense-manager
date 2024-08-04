<?php

namespace App\Filament\Resources\AnnualStatementResource\Pages;

use App\Filament\Resources\AnnualStatementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnnualStatements extends ListRecords
{
    protected static string $resource = AnnualStatementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
