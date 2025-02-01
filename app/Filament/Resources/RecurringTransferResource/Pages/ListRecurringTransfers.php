<?php

namespace App\Filament\Resources\RecurringTransferResource\Pages;

use App\Filament\Concerns\UserFilterable;
use App\Filament\Resources\RecurringTransferResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRecurringTransfers extends ListRecords
{
    use UserFilterable;

    protected static string $resource = RecurringTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
