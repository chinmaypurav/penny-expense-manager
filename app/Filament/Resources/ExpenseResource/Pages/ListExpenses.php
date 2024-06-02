<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Imports\ExpenseImporter;
use App\Filament\Resources\ExpenseResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(ExpenseImporter::class),
        ];
    }
}
