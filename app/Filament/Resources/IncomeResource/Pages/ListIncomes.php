<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Imports\IncomeImporter;
use App\Filament\Resources\IncomeResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

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

    public function filterTableQuery(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }
}
