<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Filament\Imports\PersonImporter;
use App\Filament\Resources\PersonResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListPeople extends ListRecords
{
    protected static string $resource = PersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(PersonImporter::class),
        ];
    }
}
