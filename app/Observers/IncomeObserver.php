<?php

namespace App\Observers;

use App\Models\Income;

class IncomeObserver
{
    public function creating(Income $income): void
    {
        $transactedAt = $income->transacted_at->startOfDay();

        if (
            today()->greaterThanOrEqualTo($transactedAt)
            && $income->account->initial_date->greaterThanOrEqualTo($transactedAt)
        ) {
            $income->account->update([
                'current_balance' => $income->account->current_balance + $income->amount,
                'initial_date' => $transactedAt,
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
}
