<?php

namespace App\Jobs;

use App\Enums\Frequency;
use App\Models\Expense;
use App\Models\RecurringExpense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TransactRecurringExpenseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Frequency $frequency) {}

    public function handle(): void
    {
        DB::transaction(function () {
            $fillable = (new Expense)->getFillable();

            RecurringExpense::query()
                ->where('frequency', $this->frequency)
                ->get()
                ->map(function (RecurringExpense $recurringExpense) use ($fillable) {
                    $this->processRecurringExpenseState($recurringExpense);

                    if (is_null($recurringExpense->account_id)) {
                        return;
                    }

                    $data = $recurringExpense->only($fillable);

                    $data['transacted_at'] = $recurringExpense->next_transaction_at;
                    $expense = Expense::create($data);

                    $expense->tags()->attach($recurringExpense->tags);

                });
        });
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
