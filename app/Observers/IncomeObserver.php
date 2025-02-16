<?php

namespace App\Observers;

use App\Models\Income;

class IncomeObserver
{
    public function creating(Income $income): void
    {
        $income->account()->increment('current_balance', $income->amount);
    }

    public function updating(Income $income): void
    {
        $originalAmount = $income->getOriginal('amount');
        $modifiedAmount = $income->getAttribute('amount');

        $diff = $originalAmount - $modifiedAmount;

        $income->account()->decrement('current_balance', $diff);
    }

    public function deleting(Income $income): void
    {
        $income->account()->decrement('current_balance', $income->amount);
    }
}
