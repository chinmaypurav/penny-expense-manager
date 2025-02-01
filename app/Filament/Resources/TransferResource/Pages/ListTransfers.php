<?php

namespace App\Filament\Resources\TransferResource\Pages;

use App\Filament\Concerns\UserFilterable;
use App\Filament\Imports\TransferImporter;
use App\Filament\Resources\TransferResource;
use App\Models\Transfer;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListTransfers extends ListRecords
{
    use UserFilterable;

    protected static string $resource = TransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(TransferImporter::class)
                ->visible(auth()->user()->can('import', Transfer::class)),
        ];
    }
}
