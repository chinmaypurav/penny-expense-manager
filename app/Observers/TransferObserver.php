<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transfer;
use Carbon\Carbon;

readonly class TransferObserver
{
    public function created(Transfer $transfer): void
    {
        $creditBalance = $this->getCreditBalance(
            $transfer->creditor->current_balance, $transfer->amount
        );
        $debitBalance = $this->getDebitBalance(
            $transfer->debtor->current_balance, $transfer->amount
        );

        if ($transactedAt = $this->shouldUpdateCreditorInitialDate($transfer)) {
            $transfer->creditor->update([
                'current_balance' => $creditBalance,
                'initial_date' => $transactedAt,
            ]);
        } else {
            $transfer->creditor()->increment('current_balance', $transfer->amount);
        }

        if ($transactedAt = $this->shouldUpdateDebtorInitialDate($transfer)) {
            $transfer->debtor->update([
                'current_balance' => $debitBalance,
                'initial_date' => $transactedAt,
            ]);
        } else {
            $transfer->debtor()->decrement('current_balance', $transfer->amount);
        }
    }

    public function updating(Transfer $transfer): void
    {
        $oldAmount = $transfer->getOriginal('amount');
        $newAmount = $transfer->getAttribute('amount');
        $diff = 0;

        if ($transfer->isDirty('amount')) {
            $diff = $oldAmount - $newAmount;
            $transfer->debtor()->increment('current_balance', $diff);
            $transfer->creditor()->decrement('current_balance', $diff);
        }

        if ($transfer->isDirty('creditor_id')) {
            $oldAccount = Account::find($transfer->getOriginal('creditor_id'));
            $newAccount = Account::find($transfer->getAttribute('creditor_id'));

            $oldAccount->newQuery()->decrement('current_balance', $oldAmount);
            $newAccount->newQuery()->increment('current_balance', $newAmount + $diff);
        }

        if ($transfer->isDirty('debtor_id')) {
            $oldAccount = Account::find($transfer->getOriginal('debtor_id'));
            $newAccount = Account::find($transfer->getAttribute('debtor_id'));

            $oldAccount->newQuery()->increment('current_balance', $oldAmount);
            $newAccount->newQuery()->decrement('current_balance', $newAmount + $diff);
        }

    }

    public function deleted(Transfer $transfer): void
    {
        $transfer->debtor()->increment('current_balance', $transfer->amount);
        $transfer->creditor()->decrement('current_balance', $transfer->amount);
    }

    private function shouldUpdateCreditorInitialDate(Transfer $transfer): ?Carbon
    {
        $transactedAt = $this->getTransactedAt($transfer);

        if (Carbon::today()->lessThan($transactedAt)) {
            return null;
        }

        if ($transfer->creditor->initial_date->lessThan($transactedAt)) {
            return null;
        }

        return $transactedAt;
    }

    private function shouldUpdateDebtorInitialDate(Transfer $transfer): ?Carbon
    {
        $transactedAt = $this->getTransactedAt($transfer);

        if (Carbon::today()->lessThan($transactedAt)) {
            return null;
        }

        if ($transfer->debtor->initial_date->lessThan($transactedAt)) {
            return null;
        }

        return $transactedAt;
    }

    private function getTransactedAt(Transfer $transfer): Carbon
    {
        return $transfer->transacted_at->startOfDay();
    }

    private function getCreditBalance(float $currentBalance, float $amount): float
    {
        return $currentBalance + $amount;
    }

    private function getDebitBalance(float $currentBalance, float $amount): float
    {
        return $currentBalance - $amount;
    }
}
