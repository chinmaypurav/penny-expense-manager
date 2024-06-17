<?php

namespace App\Filament\Concerns;

use Filament\Actions\Imports\ImportColumn;

trait IncomeExpenseImporterTrait
{
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('user')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name')
                ->rules(['required']),
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
                ->requiredMapping()
                ->rules(['required', 'datetime']),
            ImportColumn::make('amount')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }
}
