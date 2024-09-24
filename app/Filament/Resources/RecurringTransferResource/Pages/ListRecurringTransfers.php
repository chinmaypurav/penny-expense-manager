<?php

namespace App\Filament\Resources\RecurringTransferResource\Pages;

use App\Filament\Resources\RecurringTransferResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListRecurringTransfers extends ListRecords
{
    protected static string $resource = RecurringTransferResource::class;

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
