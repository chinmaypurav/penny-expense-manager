<?php

namespace App\Jobs;

use App\Enums\Frequency;
use App\Models\Income;
use App\Models\RecurringIncome;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TransactRecurringIncomeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Frequency $frequency) {}

    public function handle(): void
    {
        DB::transaction(function () {
            $fillable = (new Income)->getFillable();

            RecurringIncome::query()
                ->where('frequency', $this->frequency)
                ->get()
                ->map(function (RecurringIncome $recurringIncome) use ($fillable) {
                    $data = $recurringIncome->only($fillable);

                    $data['transacted_at'] = $recurringIncome->next_transaction_at;
                    $income = Income::create($data);

                    $income->tags()->attach($recurringIncome->tags);

                    $this->processRecurringIncomeState($recurringIncome);
                });
        });
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
