<?php

namespace App\Observers;

use App\Models\Account;

class AccountObserver
{
    public function created(Account $account): void
    {
        $account->balances()->create([
            'is_initial_record' => true,
            'balance' => $account->current_balance,
            'recorded_until' => today()->subDay(),
        ]);
    }
}
