<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Imports\TagImporter;
use App\Filament\Resources\TagResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(TagImporter::class),
        ];
    }
}
