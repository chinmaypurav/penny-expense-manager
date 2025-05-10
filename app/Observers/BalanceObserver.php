<?php

namespace App\Observers;

use App\Models\Balance;
use App\Services\AccountTransactionService;

class BalanceObserver
{
    public function created(Balance $balance): void
    {
        app(AccountTransactionService::class)->sendTransactionsOverEmail($balance, $balance->account->user);
    }
}
