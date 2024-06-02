<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Imports\IncomeImporter;
use App\Filament\Resources\IncomeResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListIncomes extends ListRecords
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(IncomeImporter::class),
        ];
    }
}
