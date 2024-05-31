<?php

namespace App\Filament\Resources\BalanceResource\Pages;

use App\Filament\Resources\BalanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBalances extends ListRecords
{
    protected static string $resource = BalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
