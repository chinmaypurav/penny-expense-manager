<?php

namespace App\Services;

use App\Enums\PanelId;
use App\Models\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TableFilterService
{
    public static function getAccountsFilter(int $userId): Collection
    {
        return Account::query()
            ->when(
                PanelId::APP->isCurrentPanel(),
                fn (Builder $q) => $q->where('user_id', $userId)
            )
            ->orderBy('name')
            ->pluck('name', 'id');
    }
}
