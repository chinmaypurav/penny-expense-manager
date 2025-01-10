<?php

namespace App\Services;

use App\Models\RecurringExpense;
use App\Models\RecurringIncome;
use App\Models\User;
use Carbon\Carbon;

class RecurringCashFlowService
{
    public static function processRecurringIncomes(User $user, Carbon $startDate, Carbon $endDate): float
    {
        return RecurringIncome::query()
            ->where('user_id', $user->id)
            ->whereDate('next_transaction_at', '>=', $startDate)
            ->whereDate('next_transaction_at', '<=', $endDate)
            ->get()
            ->reduce(function (?float $carry, RecurringIncome $recurringIncome) use ($endDate) {
                $count = $recurringIncome->frequency->getRemainingIterations(
                    $recurringIncome->next_transaction_at,
                    $endDate,
                    $recurringIncome->remaining_recurrences
                );

                return $carry + $count * $recurringIncome->amount;
            });
    }

    public static function processRecurringExpenses(User $user, Carbon $startDate, Carbon $endDate): float
    {
        return RecurringExpense::query()
            ->where('user_id', $user->id)
            ->whereDate('next_transaction_at', '>=', $startDate)
            ->whereDate('next_transaction_at', '<=', $endDate)
            ->get()
            ->reduce(function (?float $carry, RecurringExpense $recurringExpense) use ($endDate) {
                $count = $recurringExpense->frequency->getRemainingIterations(
                    $recurringExpense->next_transaction_at,
                    $endDate,
                    $recurringExpense->remaining_recurrences
                );

                return $carry + $count * $recurringExpense->amount;
            });
    }
}
