<?php

namespace App\Filament\Resources\RecurringTransferResource\Pages;

use App\Filament\Resources\RecurringTransferResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecurringTransfer extends CreateRecord
{
    protected static string $resource = RecurringTransferResource::class;

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
