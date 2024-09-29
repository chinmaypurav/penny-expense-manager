<?php

namespace App\Filament\Resources\RecurringExpenseResource\Pages;

use App\Filament\Resources\RecurringExpenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecurringExpense extends CreateRecord
{
    protected static string $resource = RecurringExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
