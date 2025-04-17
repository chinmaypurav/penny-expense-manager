<?php

namespace App\Jobs;

use App\Models\RecurringTransfer;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransactRecurringTransferJob extends AbstractTransactRecurringJob
{
    public function __construct(public RecurringTransfer $recurringTransfer) {}

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $this->processRecurringTransactionState($this->recurringTransfer);

            $transfer = Transfer::create($this->getTransactionData($this->recurringTransfer));

            $this->attachTags($transfer, $this->recurringTransfer->tags);
        });
    }

    protected function getFillable(): array
    {
        return (new Transfer)->getFillable();
    }
}
