<?php

namespace App\Jobs;

use App\Models\RecurringExpense;
use App\Models\RecurringIncome;
use App\Models\RecurringTransfer;
use Carbon\CarbonInterface as Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TriggerRecurringTransactionsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Carbon $today) {}

    public function handle(): void
    {
        RecurringIncome::query()
            ->whereDate('next_transaction_at', $this->today)
            ->with('tags')
            ->get()
            ->each(fn (RecurringIncome $recurringIncome) => TransactRecurringIncomeJob::dispatch($recurringIncome));

        RecurringExpense::query()
            ->whereDate('next_transaction_at', $this->today)
            ->with('tags')
            ->get()
            ->each(fn (RecurringExpense $recurringExpense) => TransactRecurringExpenseJob::dispatch($recurringExpense));

        RecurringTransfer::query()
            ->whereDate('next_transaction_at', $this->today)
            ->with('tags')
            ->get()
            ->each(fn (RecurringTransfer $recurringTransfer) => TransactRecurringTransferJob::dispatch($recurringTransfer));
    }
}
