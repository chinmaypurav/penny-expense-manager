<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountTransactionService;

class TransactionController extends Controller
{
    public function __invoke(Account $account, AccountTransactionService $service)
    {
        $transactions = $service->getTransactions($account, $account->initial_date, now())->reverse();

        return view('transactions.index', [
            'account' => $account,
            'transactions' => $transactions,
        ]);
    }
}
