<?php

namespace App\Observers;

use App\Models\Income;

class IncomeObserver
{
    public function creating(Income $expense): void
    {
        $expense->account()->increment('balance', $expense->amount);
    }

    public function updating(Income $income): void
    {
        $originalAmount = $income->getOriginal('amount');
        $modifiedAmount = $income->getAttribute('amount');

        $diff = $originalAmount - $modifiedAmount;

        $income->account()->decrement('balance', $diff);
    }

    public function deleting(Income $income): void
    {
        $income->account()->decrement('balance', $income->amount);
    }
}
