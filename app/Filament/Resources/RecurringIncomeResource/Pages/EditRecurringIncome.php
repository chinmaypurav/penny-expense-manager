<?php

namespace App\Filament\Resources\RecurringIncomeResource\Pages;

use App\Filament\Resources\RecurringIncomeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRecurringIncome extends EditRecord
{
    protected static string $resource = RecurringIncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
