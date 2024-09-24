<?php

namespace App\Filament\Resources\RecurringTransferResource\Pages;

use App\Filament\Resources\RecurringTransferResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRecurringTransfer extends EditRecord
{
    protected static string $resource = RecurringTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
