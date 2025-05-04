<?php

namespace App\Filament\Concerns;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
use Carbon\CarbonInterface as Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Income|Expense|Transfer
 */
trait Transactional
{
    public static function scopeToday(Builder $q): void
    {
        $q->whereDate('transacted_at', today());
    }

    #[Scope]
    protected function transactionBetween(Builder $builder, Carbon $startDate, Carbon $endDate): void
    {
        $builder
            ->where('transacted_at', '>=', $startDate)
            ->where('transacted_at', '<=', $endDate);
    }
}
