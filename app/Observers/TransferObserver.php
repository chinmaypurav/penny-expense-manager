<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transfer;

readonly class TransferObserver
{
    public function created(Transfer $transfer): void
    {
        $transfer->debtor()->decrement('balance', $transfer->amount);
        $transfer->creditor()->increment('balance', $transfer->amount);
    }

    public function updating(Transfer $transfer): void
    {
        $oldAmount = $transfer->getOriginal('amount');
        $newAmount = $transfer->getAttribute('amount');
        $diff = 0;

        if ($transfer->isDirty('amount')) {
            $diff = $oldAmount - $newAmount;
            $transfer->debtor()->increment('balance', $diff);
            $transfer->creditor()->decrement('balance', $diff);
        }

        if ($transfer->isDirty('creditor_id')) {
            $oldAccount = Account::find($transfer->getOriginal('creditor_id'));
            $newAccount = Account::find($transfer->getAttribute('creditor_id'));

            $oldAccount->newQuery()->decrement('balance', $oldAmount);
            $newAccount->newQuery()->increment('balance', $newAmount + $diff);
        }

        if ($transfer->isDirty('debtor_id')) {
            $oldAccount = Account::find($transfer->getOriginal('debtor_id'));
            $newAccount = Account::find($transfer->getAttribute('debtor_id'));

            $oldAccount->newQuery()->increment('balance', $oldAmount);
            $newAccount->newQuery()->decrement('balance', $newAmount + $diff);
        }

    }

    public function deleted(Transfer $transfer): void
    {
        $transfer->debtor()->increment('balance', $transfer->amount);
        $transfer->creditor()->decrement('balance', $transfer->amount);
    }
}
