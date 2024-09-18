<?php

namespace App\Filament\Resources\BalanceResource\Pages;

use App\Filament\Resources\BalanceResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListBalances extends ListRecords
{
    protected static string $resource = BalanceResource::class;

    public function filterTableQuery(Builder $query): Builder
    {
        return parent::filterTableQuery($query)
            ->whereIn('account_id', auth()->user()->accounts()->pluck('id'));
    }
}
