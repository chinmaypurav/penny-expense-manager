<?php

namespace App\Observers;

use App\Models\Expense;

class ExpenseObserver extends TransactionObserver
{
    public function deleting(Expense $expense): void
    {
        $expense->account()->increment('current_balance', $expense->amount);
    }

    protected function getCurrentBalance(float $existingBalance, float $difference): float
    {
        return $existingBalance - $difference;
    }
}
