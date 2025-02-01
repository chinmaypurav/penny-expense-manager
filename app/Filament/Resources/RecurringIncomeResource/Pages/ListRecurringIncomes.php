<?php

namespace App\Filament\Resources\RecurringIncomeResource\Pages;

use App\Filament\Concerns\UserFilterable;
use App\Filament\Resources\RecurringIncomeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRecurringIncomes extends ListRecords
{
    use UserFilterable;

    protected static string $resource = RecurringIncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
