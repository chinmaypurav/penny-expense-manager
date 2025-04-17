<?php

namespace App\Jobs;

use App\Models\Income;
use App\Models\RecurringIncome;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransactRecurringIncomeJob extends AbstractTransactRecurringJob
{
    public function __construct(public RecurringIncome $recurringIncome) {}

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $this->processRecurringTransactionState($this->recurringIncome);

            if (is_null($this->recurringIncome->account_id)) {
                return;
            }

            $income = Income::create($this->getTransactionData($this->recurringIncome));

            $this->attachTags($income, $this->recurringIncome->tags);
        });
    }

    protected function getFillable(): array
    {
        return (new Income)->getFillable();
    }
}
