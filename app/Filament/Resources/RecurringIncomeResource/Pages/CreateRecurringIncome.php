<?php

namespace App\Filament\Resources\RecurringIncomeResource\Pages;

use App\Filament\Resources\RecurringIncomeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecurringIncome extends CreateRecord
{
    protected static string $resource = RecurringIncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

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
