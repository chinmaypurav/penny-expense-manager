<?php

namespace App\Observers;

use App\Models\Income;

class IncomeObserver
{
    public function creating(Income $income): void
    {
        if ($income->transacted_at->lessThanOrEqualTo(today())) {
            $income->account->update([
                'current_balance' => $income->account->current_balance + $income->amount,
                'initial_date' => $income->transacted_at->startOfDay(),
            ]);

            return;
        }

        $income->account()->increment('current_balance', $income->amount);
    }

    public function updating(Income $income): void
    {
        $originalAmount = $income->getOriginal('amount');
        $modifiedAmount = $income->getAttribute('amount');

        $diff = $originalAmount - $modifiedAmount;

        if ($income->transacted_at->lessThanOrEqualTo(today())) {
            $income->account->update([
                'current_balance' => $income->account->current_balance - $diff,
                'initial_date' => $income->transacted_at->startOfDay(),
            ]);

            return;
        }

        $income->account()->decrement('current_balance', $diff);
    }

    public function deleting(Income $income): void
    {
        $income->account()->decrement('current_balance', $income->amount);
    }
}
