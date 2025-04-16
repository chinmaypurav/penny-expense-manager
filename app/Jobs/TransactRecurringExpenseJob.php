<?php

namespace App\Jobs;

use App\Models\Expense;
use App\Models\RecurringExpense;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class TransactRecurringExpenseJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public RecurringExpense $recurringExpense) {}

    public function handle(): void
    {
        DB::transaction(function () {
            $this->processRecurringExpenseState($this->recurringExpense);

            if (is_null($this->recurringExpense->account_id)) {
                return;
            }

            $data = $this->recurringExpense->only($this->getFillable());

            $data['transacted_at'] = $this->recurringExpense->next_transaction_at;
            $expense = Expense::create($data);

            $expense->tags()->attach($this->recurringExpense->tags);
        });
    }

    protected function getFillable(): array
    {
        return (new Expense)->getFillable();
    }

    private function processRecurringExpenseState(RecurringExpense $recurringExpense): void
    {
        if ($recurringExpense->remaining_recurrences === null) {
            return;
        }

        if ($recurringExpense->remaining_recurrences === 1) {
            $recurringExpense->delete();

            return;
        }

        $recurringExpense->decrement('remaining_recurrences');
    }
}
