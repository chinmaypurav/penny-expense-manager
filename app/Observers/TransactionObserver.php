<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;

abstract class TransactionObserver
{
    public function creating(Income|Expense $transaction): void
    {
        $currentBalance = $this->getCurrentBalance(
            $transaction->account->current_balance, $transaction->amount
        );

        if ($transactedAt = $this->shouldUpdateAccountInitialDate($transaction)) {
            $transaction->account->update([
                'current_balance' => $currentBalance,
                'initial_date' => $transactedAt,
            ]);

            return;
        }

        $transaction->account()->update([
            'current_balance' => $currentBalance,
        ]);
    }

    public function updating(Income|Expense $transaction): void
    {
        $diff = $this->getUpdatedAmountDiff($transaction);

        $currentBalance = $this->getCurrentBalance(
            $transaction->account->current_balance, $diff
        );

        if ($transactedAt = $this->shouldUpdateAccountInitialDate($transaction)) {
            $transaction->account->update([
                'current_balance' => $currentBalance,
                'initial_date' => $transactedAt,
            ]);

            return;
        }

        $transaction->account->update([
            'current_balance' => $currentBalance,
        ]);
    }

    private function getTransactedAt(Income|Expense $transaction): Carbon
    {
        return $transaction->transacted_at->startOfDay();
    }

    protected function shouldUpdateAccountInitialDate(Income|Expense $transaction): ?Carbon
    {
        $transactedAt = $this->getTransactedAt($transaction);

        if (Carbon::today()->lessThan($transactedAt)) {
            return null;
        }

        if ($transaction->account->initial_date->lessThan($transactedAt)) {
            return null;
        }

        return $transactedAt;
    }

    private function getUpdatedAmountDiff(Income|Expense $transaction): float
    {
        $originalAmount = $transaction->getOriginal('amount');
        $changedAmount = $transaction->getAttribute('amount');

        return $changedAmount - $originalAmount;
    }

    abstract protected function getCurrentBalance(float $existingBalance, float $difference): float;
}
