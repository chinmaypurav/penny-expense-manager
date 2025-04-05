<?php

namespace App\Filament\Concerns;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
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
}
