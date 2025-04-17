<?php

namespace App\Jobs;

use App\Models\Expense;
use App\Models\RecurringExpense;
use Illuminate\Support\Facades\DB;

class TransactRecurringExpenseJob extends AbstractTransactRecurringJob
{
    public function __construct(public RecurringExpense $recurringExpense) {}

    public function handle(): void
    {
        DB::transaction(function () {
            $this->processRecurringTransactionState($this->recurringExpense);

            if (is_null($this->recurringExpense->account_id)) {
                return;
            }

            $expense = Expense::create($this->getTransactionData($this->recurringExpense));

            $this->attachTags($expense, $this->recurringExpense->tags);
        });
    }

    protected function getFillable(): array
    {
        return (new Expense)->getFillable();
    }
}
