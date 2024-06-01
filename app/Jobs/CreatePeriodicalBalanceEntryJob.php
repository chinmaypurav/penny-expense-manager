<?php

namespace App\Jobs;

use App\Enums\RecordType;
use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class CreatePeriodicalBalanceEntryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly RecordType $recordType, private readonly Carbon $today)
    {
    }

    public function handle(): void
    {
        $accounts = Account::all();

        foreach ($accounts as $account) {
            $account->balances()->create([
                'balance' => $this->getBalanceForYesterday($account),
                'record_type' => $this->recordType,
                'recorded_until' => $this->today->subDay(),
            ]);
        }
    }

    private function getBalanceForYesterday(Account $account): float
    {
        $incomeTotal = $account->incomes()->where('transacted_at', $this->today->subDay())->sum('amount');
        $expenseTotal = $account->expenses()->where('transacted_at', $this->today)->sum('amount');

        return $account->current_balance - $incomeTotal + $expenseTotal;
    }
}
