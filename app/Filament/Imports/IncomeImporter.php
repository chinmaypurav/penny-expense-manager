<?php

namespace App\Filament\Imports;

use App\Filament\Concerns\IncomeExpenseImporterTrait;
use App\Models\Income;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class IncomeImporter extends Importer
{
    use IncomeExpenseImporterTrait;

    protected static ?string $model = Income::class;

    public function resolveRecord(): ?Income
    {
        // return Income::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Income;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your income import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
