<?php

namespace App\Filament\Resources;

use App\Enums\PanelId;
use App\Models\Expense;
use App\Models\Income;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class TransactionResource extends Resource
{
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->when(PanelId::APP->isCurrentPanel(), fn (Builder $q) => $q->where('user_id', auth()->id()))
            ->with(['user', 'person', 'account']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'person.name', 'account.name', 'description'];
    }

    public static function getGlobalSearchResultDetails(Model|Income|Expense $record): array
    {
        if ($record->user && PanelId::FAMILY->isCurrentPanel()) {
            $details['User'] = $record->user->name;
        }

        if ($record->person) {
            $details['Person'] = $record->person->name;
        }

        if ($record->account) {
            $details['Account'] = $record->account->name;
        }

        $details['Description'] = $record->description;

        return $details;
    }
}
