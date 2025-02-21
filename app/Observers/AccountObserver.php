<?php

namespace App\Observers;

use App\Enums\RecordType;
use App\Models\Account;

class AccountObserver
{
    public function created(Account $account): void
    {
        $account->balances()->create([
            'is_initial_record' => true,
            'balance' => $account->current_balance,
            'recorded_until' => $account->initial_date,
            'record_type' => RecordType::INITIAL,
        ]);
    }

    public function updated(Account $account): void
    {
        if ($account->isDirty('initial_date') && $account->isDirty('current_balance')) {
            $diff = $this->getBalanceDifference($account);
            $account->initialBalance->update([
                'recorded_until' => $account->initial_date->startOfDay(),
                'balance' => $account->initialBalance->balance - $diff,
            ]);

            return;
        }

        if ($account->isDirty('initial_date')) {
            $oldBalance = $account->getOriginal('current_balance');
            $newBalance = $account->getAttribute('current_balance');
            $diff = $newBalance - $oldBalance;

            $account->initialBalance()->update([
                'recorded_until' => $account->initial_date->startOfDay(),
            ]);

            $account->balances()->increment('balance', $diff);

            return;
        }

        if ($account->isDirty('current_balance')) {
            $account->initialBalance()->increment('balance', $this->getBalanceDifference($account));
        }
    }

    private function getBalanceDifference(Account $account): int
    {
        $oldBalance = $account->getOriginal('current_balance');
        $newBalance = $account->getAttribute('current_balance');

        return $newBalance - $oldBalance;
    }
}
