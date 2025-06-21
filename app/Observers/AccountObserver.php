<?php

namespace App\Observers;

use App\Models\Account;

class AccountObserver
{
    public function creating(Account $account): void
    {
        $account->setAttribute('current_balance', $account->getAttribute('initial_balance'));
    }
}
