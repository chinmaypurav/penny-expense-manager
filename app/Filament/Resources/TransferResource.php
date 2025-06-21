<?php

namespace App\Filament\Resources;

use App\Enums\PanelId;
use App\Filament\Concerns\BulkDeleter;
use App\Filament\Concerns\UserFilterable;
use App\Filament\Resources\TransferResource\Pages\CreateTransfer;
use App\Filament\Resources\TransferResource\Pages\EditTransfer;
use App\Filament\Resources\TransferResource\Pages\ListTransfers;
use App\Filament\Resources\TransferResource\TransferForm;
use App\Filament\Resources\TransferResource\TransferTable;
use App\Models\Transfer;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TransferResource extends Resource
{
    use BulkDeleter, UserFilterable;

    protected static ?string $model = Transfer::class;

    protected static ?string $slug = 'transfers';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function form(Schema $schema): Schema
    {
        return TransferForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransferTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransfers::route('/'),
            'create' => CreateTransfer::route('/create'),
            'edit' => EditTransfer::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->when(PanelId::APP->isCurrentPanel(), fn (Builder $q) => $q->where('user_id', auth()->id()))
            ->with(['user', 'creditor', 'debtor']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'creditor.name', 'debtor.name', 'description'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->user && PanelId::FAMILY->isCurrentPanel()) {
            $details['User'] = $record->user->name;
        }

        if ($record->creditor) {
            $details['Creditor'] = $record->creditor->name;
        }

        if ($record->debtor) {
            $details['Debtor'] = $record->debtor->name;
        }

        $details['Description'] = $record->description;

        return $details;
    }
}
