<?php

namespace App\Jobs;

use App\Models\Income;
use App\Models\RecurringIncome;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransactRecurringIncomeJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public RecurringIncome $recurringIncome) {}

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $this->processRecurringIncomeState($this->recurringIncome);

            if (is_null($this->recurringIncome->account_id)) {
                return;
            }

            $data = $this->recurringIncome->only($this->getFillable());

            $data['transacted_at'] = $this->recurringIncome->next_transaction_at;
            $income = Income::create($data);

            $income->tags()->attach($this->recurringIncome->tags);
        });
    }

    protected function getFillable(): array
    {
        return (new Income)->getFillable();
    }

    private function processRecurringIncomeState(RecurringIncome $recurringIncome): void
    {
        if ($recurringIncome->remaining_recurrences === null) {
            return;
        }

        if ($recurringIncome->remaining_recurrences === 1) {
            $recurringIncome->delete();

            return;
        }

        $recurringIncome->decrement('remaining_recurrences');
    }
}
