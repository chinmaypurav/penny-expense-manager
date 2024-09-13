<?php

namespace App\Filament\Imports;

use App\Filament\Concerns\IncomeExpenseImporterTrait;
use App\Models\Expense;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ExpenseImporter extends Importer
{
    use IncomeExpenseImporterTrait;

    protected static ?string $model = Expense::class;

    public function resolveRecord(): ?Expense
    {
        // return Expense::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Expense;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your expense import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
