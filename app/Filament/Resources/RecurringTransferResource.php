<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\BulkDeleter;
use App\Filament\Concerns\UserFilterable;
use App\Filament\Resources\RecurringTransferResource\Pages\CreateRecurringTransfer;
use App\Filament\Resources\RecurringTransferResource\Pages\EditRecurringTransfer;
use App\Filament\Resources\RecurringTransferResource\Pages\ListRecurringTransfers;
use App\Filament\Resources\RecurringTransferResource\RecurringTransferForm;
use App\Filament\Resources\RecurringTransferResource\RecurringTransferTable;
use App\Models\RecurringTransfer;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class RecurringTransferResource extends Resource
{
    use BulkDeleter, UserFilterable;

    protected static ?string $model = RecurringTransfer::class;

    protected static ?string $slug = 'recurring-transfers';

    protected static string|\UnitEnum|null $navigationGroup = 'Recurring Transactions';

    public static function form(Schema $schema): Schema
    {
        return RecurringTransferForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecurringTransferTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecurringTransfers::route('/'),
            'create' => CreateRecurringTransfer::route('/create'),
            'edit' => EditRecurringTransfer::route('/{record}/edit'),
        ];
    }
}
