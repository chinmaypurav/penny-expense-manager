<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Concerns\UserFilterable;
use App\Filament\Imports\IncomeImporter;
use App\Filament\Resources\IncomeResource;
use App\Models\Income;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListIncomes extends ListRecords
{
    use UserFilterable;

    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(IncomeImporter::class)
                ->visible(auth()->user()->can('import', Income::class)),
        ];
    }
}
