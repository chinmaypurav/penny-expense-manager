<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transfer;

readonly class TransferObserver
{
    public function created(Transfer $transfer): void
    {
        $transfer->debtor()->decrement('current_balance', $transfer->amount);
        $transfer->creditor()->increment('current_balance', $transfer->amount);
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
}
