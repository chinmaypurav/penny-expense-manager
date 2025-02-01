<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Concerns\UserFilterable;
use App\Filament\Imports\ExpenseImporter;
use App\Filament\Resources\ExpenseResource;
use App\Models\Expense;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListExpenses extends ListRecords
{
    use UserFilterable;

    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(ExpenseImporter::class)
                ->visible(auth()->user()->can('import', Expense::class)),
        ];
    }
}
