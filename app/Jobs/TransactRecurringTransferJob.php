<?php

namespace App\Jobs;

use App\Enums\Frequency;
use App\Models\RecurringTransfer;
use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TransactRecurringTransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Frequency $frequency) {}

    public function handle(): void
    {
        DB::transaction(function () {
            $fillable = (new Transfer)->getFillable();

            RecurringTransfer::query()
                ->where('frequency', $this->frequency)
                ->get()
                ->map(function (RecurringTransfer $recurringTransfer) use ($fillable) {
                    $data = $recurringTransfer->only($fillable);

                    $data['transacted_at'] = $recurringTransfer->next_transaction_at;
                    $income = Transfer::create($data);

                    $income->tags()->attach($recurringTransfer->tags);

                    $this->processRecurringTransferState($recurringTransfer);
                });
        });
    }

    private function processRecurringTransferState(RecurringTransfer $recurringTransfer): void
    {
        if ($recurringTransfer->remaining_recurrences === null) {
            return;
        }

        if ($recurringTransfer->remaining_recurrences === 1) {
            $recurringTransfer->delete();

            return;
        }

        $recurringTransfer->decrement('remaining_recurrences');
    }
}
