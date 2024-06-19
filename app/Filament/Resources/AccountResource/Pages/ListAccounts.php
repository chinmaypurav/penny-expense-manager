<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Imports\AccountImporter;
use App\Filament\Resources\AccountResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAccounts extends ListRecords
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(AccountImporter::class),
        ];
    }

    public function filterTableQuery(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }
}
