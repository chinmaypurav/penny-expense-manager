<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;

abstract class TransactionObserver
{
    public function created(Income|Expense $transaction): void
    {
        $currentBalance = $this->getCurrentBalanceWhenCreated(
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

    public function updated(Income|Expense $transaction): void
    {
        if ($transaction->isClean(['amount', 'transacted_at'])) {
            return;
        }

        $diff = $this->getUpdatedAmountDiff($transaction);

        $currentBalance = $this->getCurrentBalanceWhenCreated(
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

    public function deleted(Income|Expense $transaction): void
    {
        $currentBalance = $this->getCurrentBalanceWhenDeleted(
            $transaction->account->current_balance, $transaction->amount
        );

        $transaction->account()->update([
            'current_balance' => $currentBalance,
        ]);
    }

    private function getTransactedAt(Income|Expense $transaction): Carbon
    {
        return $transaction->transacted_at->startOfDay();
    }

    private function shouldUpdateAccountInitialDate(Income|Expense $transaction): ?Carbon
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

    abstract protected function getCurrentBalanceWhenCreated(float $existingBalance, float $difference): float;

    abstract protected function getCurrentBalanceWhenDeleted(float $existingBalance, float $difference): float;
}
