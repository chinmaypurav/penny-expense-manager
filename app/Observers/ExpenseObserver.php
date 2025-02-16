<?php

namespace App\Observers;

use App\Models\Expense;

class ExpenseObserver
{
    public function creating(Expense $expense): void
    {
        if ($expense->transacted_at->lessThanOrEqualTo(today())) {
            $expense->account->update([
                'current_balance' => $expense->account->current_balance - $expense->amount,
                'initial_date' => $expense->transacted_at->startOfDay(),
            ]);

            return;
        }

        $expense->account()->decrement('current_balance', $expense->amount);
    }

    public function updating(Expense $expense): void
    {
        $originalAmount = $expense->getOriginal('amount');
        $modifiedAmount = $expense->getAttribute('amount');

        $diff = $originalAmount - $modifiedAmount;

        if ($expense->transacted_at->lessThanOrEqualTo(today())) {
            $expense->account->update([
                'current_balance' => $expense->account->current_balance + $diff,
                'initial_date' => $expense->transacted_at->startOfDay(),
            ]);

            return;
        }

        $expense->account()->increment('current_balance', $diff);
    }

    public function deleting(Expense $expense): void
    {
        $expense->account()->increment('current_balance', $expense->amount);
    }
}
