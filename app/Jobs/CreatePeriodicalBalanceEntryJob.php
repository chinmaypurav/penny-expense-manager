<?php

namespace App\Jobs;

use App\Enums\RecordType;
use App\Models\Account;
use Carbon\CarbonInterface as Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreatePeriodicalBalanceEntryJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly RecordType $recordType, public readonly Carbon $today) {}

    public function handle(): void
    {
        $today = $this->today->toImmutable();
        $startDate = $this->recordType->getStartDate($today);
        $endDate = $this->recordType->getEndDate($today);

        $accounts = Account::all();

        foreach ($accounts as $account) {
            $account->balances()->create([
                'balance' => $this->getBalanceForYesterday($account, $startDate, $endDate),
                'record_type' => $this->recordType,
                'recorded_until' => $today,
            ]);
        }
    }

    private function getBalanceForYesterday(Account $account, Carbon $startDate, Carbon $endDate): float
    {
        $incomeTotal = $account->incomes()
            ->transactionBetween($startDate, $endDate)
            ->sum('amount');

        $expenseTotal = $account->expenses()
            ->transactionBetween($startDate, $endDate)
            ->sum('amount');

        $debitTransfers = $account->debitTransfers()
            ->transactionBetween($startDate, $endDate)
            ->sum('amount');

        $creditTransfers = $account->creditTransfers()
            ->transactionBetween($startDate, $endDate)
            ->sum('amount');

        return $account->current_balance
            + $incomeTotal
            - $expenseTotal
            - $debitTransfers
            + $creditTransfers;
    }
}
