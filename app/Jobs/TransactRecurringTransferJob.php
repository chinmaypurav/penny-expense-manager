<?php

namespace App\Jobs;

use App\Models\RecurringTransfer;
use App\Models\Transfer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class TransactRecurringTransferJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public RecurringTransfer $recurringTransfer) {}

    public function handle(): void
    {
        DB::transaction(function () {
            $this->processRecurringTransferState($this->recurringTransfer);

            $data = $this->recurringTransfer->only($this->getFillable());

            $data['transacted_at'] = $this->recurringTransfer->next_transaction_at;
            $transfer = Transfer::create($data);

            $transfer->tags()->attach($this->recurringTransfer->tags);
        });
    }

    protected function getFillable(): array
    {
        return (new Transfer)->getFillable();
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
