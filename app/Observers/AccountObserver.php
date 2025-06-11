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
        if ($account->wasChanged('initial_date')) {
            $account->initialBalance()->update([
                'recorded_until' => $account->initial_date,
            ]);
        }
    }
}
