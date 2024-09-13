<?php

namespace App\Filament\Imports;

use App\Models\Transfer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TransferImporter extends Importer
{
    protected static ?string $model = Transfer::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('user')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name')
                ->rules(['required']),
            ImportColumn::make('creditor')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name')
                ->rules(['required']),
            ImportColumn::make('debtor')
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

    public function resolveRecord(): ?Transfer
    {
        // return Transfer::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Transfer;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your transfer import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
