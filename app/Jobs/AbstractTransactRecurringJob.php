<?php

namespace App\Jobs;

use App\Models\Expense;
use App\Models\Income;
use App\Models\RecurringExpense;
use App\Models\RecurringIncome;
use App\Models\RecurringTransfer;
use App\Models\Transfer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;

abstract class AbstractTransactRecurringJob implements ShouldQueue
{
    use Queueable;

    protected function processRecurringTransactionState(
        RecurringIncome|RecurringExpense|RecurringTransfer $transaction
    ): void {
        if ($transaction->remaining_recurrences === null) {
            return;
        }

        if ($transaction->remaining_recurrences === 1) {
            $transaction->delete();

            return;
        }

        $transaction->decrement('remaining_recurrences');
    }

    protected function getTransactionData(RecurringIncome|RecurringExpense|RecurringTransfer $recurringTransaction): array
    {
        $data = $recurringTransaction->only($this->getFillable());

        $data['transacted_at'] = $recurringTransaction->next_transaction_at;

        return $data;
    }

    protected function attachTags(Income|Expense|Transfer $transaction, Collection $tags): void
    {
        $transaction->tags()->attach($tags);
    }
}
