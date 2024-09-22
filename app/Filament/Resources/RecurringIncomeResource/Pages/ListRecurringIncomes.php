<?php

namespace App\Filament\Resources\RecurringIncomeResource\Pages;

use App\Filament\Resources\RecurringIncomeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListRecurringIncomes extends ListRecords
{
    protected static string $resource = RecurringIncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function filterTableQuery(Builder $query): Builder
    {
        return parent::filterTableQuery($query)
            ->where('user_id', auth()->id());
    }
}
