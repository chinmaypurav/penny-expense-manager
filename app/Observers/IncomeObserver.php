<?php

namespace App\Observers;

use App\Models\Income;

class IncomeObserver extends TransactionObserver
{
    public function deleting(Income $income): void
    {
        $income->account()->decrement('current_balance', $income->amount);
    }

    protected function getCurrentBalance(float $existingBalance, float $difference): float
    {
        return $existingBalance + $difference;
    }
}
