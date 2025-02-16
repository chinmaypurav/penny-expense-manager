<?php

namespace App\Observers;

use App\Models\Income;

class IncomeObserver extends TransactionObserver
{
    public function updating(Income $income): void
    {
        $originalAmount = $income->getOriginal('amount');
        $modifiedAmount = $income->getAttribute('amount');

        $diff = $originalAmount - $modifiedAmount;

        $transactedAt = $income->transacted_at->startOfDay();

        if (
            today()->greaterThanOrEqualTo($transactedAt)
            && $income->account->initial_date->greaterThanOrEqualTo($transactedAt)
        ) {
            $income->account->update([
                'current_balance' => $income->account->current_balance - $diff,
                'initial_date' => $transactedAt,
            ]);

            return;
        }

        $income->account()->decrement('current_balance', $diff);
    }

    public function deleting(Income $income): void
    {
        $income->account()->decrement('current_balance', $income->amount);
    }

    protected function getCurrentBalance(float $existingBalance, float $difference): float
    {
        return $existingBalance + $difference;
    }
}
