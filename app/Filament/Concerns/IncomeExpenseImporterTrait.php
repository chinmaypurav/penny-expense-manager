<?php

namespace App\Filament\Concerns;

use Filament\Actions\Imports\ImportColumn;
use Illuminate\Support\Carbon;

trait IncomeExpenseImporterTrait
{
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('person')
                ->relationship(resolveUsing: 'name'),
            ImportColumn::make('account')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name')
                ->rules(['required']),
            ImportColumn::make('category')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name')
                ->rules(['required']),
            ImportColumn::make('description')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('transacted_at')
                ->castStateUsing(function (string $state): ?Carbon {
                    return Carbon::parse($state);
                })
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('amount')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }
}
