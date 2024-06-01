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
            'recorded_until' => today()->subDay(),
            'record_type' => RecordType::INITIAL,
        ]);
    }

    public function updating(Account $account): void
    {
        if ($account->isDirty('current_balance')) {
            $oldBalance = $account->getOriginal('current_balance');
            $newBalance = $account->getAttribute('current_balance');
            $diff = $newBalance - $oldBalance;

            $account->initialBalance()->decrement('balance', $diff);
        }
    }
}
