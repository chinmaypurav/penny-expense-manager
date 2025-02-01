<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Concerns\UserFilterable;
use App\Filament\Imports\AccountImporter;
use App\Filament\Resources\AccountResource;
use App\Models\Account;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListAccounts extends ListRecords
{
    use UserFilterable;

    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(AccountImporter::class)
                ->visible(auth()->user()->can('import', Account::class)),
        ];
    }
}
