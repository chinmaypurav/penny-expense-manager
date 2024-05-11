<?php

namespace App\Observers;

use App\Models\Expense;

class ExpenseObserver
{
    public function creating(Expense $expense): void
    {
        $expense->account()->decrement('balance', $expense->amount);
    }

    public function updating(Expense $expense): void
    {
        $originalAmount = $expense->getOriginal('amount');
        $modifiedAmount = $expense->getAttribute('amount');

        $diff = $originalAmount - $modifiedAmount;

        $expense->account()->increment('balance', $diff);
    }

    public function deleting(Expense $expense): void
    {
        $expense->account()->increment('balance', $expense->amount);
    }
}
