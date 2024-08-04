<?php

namespace App\Filament\Resources\AnnualStatementResource\Pages;

use App\Filament\Resources\AnnualStatementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnnualStatement extends EditRecord
{
    protected static string $resource = AnnualStatementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
