<?php

namespace App\Filament\Imports;

use App\Models\Tag;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TagImporter extends Importer
{
    protected static ?string $model = Tag::class;

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

    public function resolveRecord(): ?Tag
    {
        return Tag::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your tag import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
