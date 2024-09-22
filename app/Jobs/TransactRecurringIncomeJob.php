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
            $recurringIncomes = RecurringIncome::query()
                ->where('frequency', $this->frequency)
                ->get();
            $fillable = (new Income)->getFillable();

            foreach ($recurringIncomes as $recurringIncome) {
                $data = $recurringIncome->only($fillable);

                $data['transacted_at'] = $recurringIncome->next_transaction_at;
                Income::create($data);

                $this->processRecurringIncomeState($recurringIncome);
            }
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
