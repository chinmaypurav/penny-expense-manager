<?php

namespace App\Filament\Imports;

use App\Models\Label;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class LabelImporter extends Importer
{
    protected static ?string $model = Label::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    private function generateRandomColor(): string
    {
        $set = '1234567890abcdef';
        $set = str_shuffle($set);

        return '#'.substr($set, 0, 6);
    }

    public function saveRecord(): void
    {
        $this->record->color = $this->generateRandomColor();
        parent::saveRecord();
    }

    public function resolveRecord(): ?Label
    {
        // return Label::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Label();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your label import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
