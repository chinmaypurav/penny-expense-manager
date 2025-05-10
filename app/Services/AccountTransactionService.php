<?php

namespace App\Services;

use App\Exports\AccountTransactionsExport;
use App\Jobs\CleanupFileJob;
use App\Jobs\SendAccountTransactionMailJob;
use App\Models\Account;
use App\Models\Balance;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AccountTransactionService
{
    public function sendTransactionsOverEmail(Balance $balance, User $user): void
    {
        $data = $this->getTransactions($balance);

        Excel::queue(new AccountTransactionsExport($data), $path = $this->getFileName($balance))->chain([
            new SendAccountTransactionMailJob($user, $path),
            new CleanupFileJob($path),
        ]);
    }

    public function getTransactions(Balance $balance): Collection
    {
        $recordType = $balance->record_type;
        $startDate = $recordType->getStartDate($balance->recorded_until->copy());
        $endDate = $balance->recorded_until;

        $incomes = Income::query()
            ->transactionBetween($startDate, $endDate)
            ->where('account_id', $balance->account_id)
            ->get();

        $expenses = Expense::query()
            ->transactionBetween($startDate, $endDate)
            ->where('account_id', $balance->account_id)
            ->get();

        $transfers = Transfer::query()
            ->transactionBetween($startDate, $endDate)
            ->where(fn (Builder $q) => $q
                ->where('creditor_id', $balance->account_id)
                ->orWhere('debtor_id', $balance->account_id)
            )->get();

        $transactions = Collection::make()
            ->concat($incomes)
            ->concat($expenses)
            ->concat($transfers)
            ->sortBy(fn (Income|Expense|Transfer $transaction) => $transaction->transacted_at);

        return $this->formatData($transactions, $balance->account);
    }

    private function formatData(Collection $transactions, Account $account): Collection
    {
        $index = 1;

        return $transactions->map(function (Income|Expense|Transfer $transaction, int $key) use (&$index, $account) {
            $class = get_class($transaction);

            $transactionType = match ($class) {
                Income::class => 'CR',
                Expense::class => 'DR',
                Transfer::class => match ($account->id) {
                    $transaction->creditor_id => 'CR',
                    $transaction->debtor_id => 'DR',
                }
            };

            return [
                'sr' => $index++,
                'date' => $transaction->transacted_at->toDateTimeString(),
                'description' => $transaction->description,
                'amount' => $transaction->amount,
                'type' => $transactionType,
            ];
        });
    }

    private function getFileName(Balance $balance): string
    {
        $account = $balance->account;

        return Str::of($account->name)
            ->append('-')
            ->append($balance->recorded_until->format('d-M-Y'))
            ->append('.csv')
            ->toString();
    }
}
