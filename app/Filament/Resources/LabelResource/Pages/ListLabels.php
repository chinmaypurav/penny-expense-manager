<?php

namespace App\Filament\Resources\LabelResource\Pages;

use App\Filament\Resources\LabelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLabels extends ListRecords
{
    protected static string $resource = LabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
