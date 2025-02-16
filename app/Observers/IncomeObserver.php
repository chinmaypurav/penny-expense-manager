<?php

namespace App\Observers;

class IncomeObserver extends TransactionObserver
{
    protected function getCurrentBalance(float $existingBalance, float $difference): float
    {
        return $existingBalance + $difference;
    }

    protected function getCurrentBalanceWhenDeleted(float $existingBalance, float $difference): float
    {
        return $existingBalance - $difference;
    }
}
