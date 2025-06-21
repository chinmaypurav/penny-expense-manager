<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Exports\AccountTransactionsExport;
use App\Jobs\CleanupFileJob;
use App\Jobs\SendAccountTransactionMailJob;
use App\Jobs\SendProvisionalAccountTransactionsMailJob;
use App\Models\Account;
use App\Models\Balance;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
use App\Models\User;
use Carbon\CarbonInterface as Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Maatwebsite\Excel\Facades\Excel;

class AccountTransactionService
{
    public function sendProvisionalTransactions(Account $account, User $user): void
    {
        $recordType = RecordType::MONTHLY;
        $balance = $account->previousBalance;
        $startDate = $recordType->getStartDate(today());
        $endDate = $balance->recorded_until;

        $data = $this->getTransactions($balance->account, $startDate, $endDate);

        Excel::queue(new AccountTransactionsExport($data), $path = $this->getFileName($balance, true))->chain([
            new SendProvisionalAccountTransactionsMailJob($user, $account, $path),
            new CleanupFileJob($path),
        ]);
    }

    public function sendTransactionsForBalancePeriod(Balance $balance, User $user): void
    {
        $recordType = $balance->record_type;
        $startDate = $recordType->getStartDate($endDate = $balance->recorded_until->subDay());

        $data = $this->getTransactions($balance->account, $startDate, $endDate);

        Excel::queue(new AccountTransactionsExport($data), $path = $this->getFileName($balance))->chain([
            new SendAccountTransactionMailJob($user, $balance, $path),
            new CleanupFileJob($path),
        ]);
    }

    public function getTransactions(Account $account, Carbon $startDate, Carbon $endDate): Collection
    {
        $incomes = Income::query()
            ->transactionBetween($startDate, $endDate)
            ->where('account_id', $account->id)
            ->get();

        $expenses = Expense::query()
            ->transactionBetween($startDate, $endDate)
            ->where('account_id', $account->id)
            ->get();

        $transfers = Transfer::query()
            ->transactionBetween($startDate, $endDate)
            ->where(fn (Builder $q) => $q
                ->where('creditor_id', $account->id)
                ->orWhere('debtor_id', $account->id)
            )->get();

        $transactions = Collection::make()
            ->concat($incomes)
            ->concat($expenses)
            ->concat($transfers)
            ->sortBy(fn (Income|Expense|Transfer $transaction) => $transaction->transacted_at);

        return $this->formatData($transactions, $account);
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

    public function getFileName(Balance $balance, bool $unaccounted = false): string
    {
        $account = $balance->account;

        return Str::of($account->name)
            ->append('-')
            ->when(
                $unaccounted,
                fn (Stringable $str) => $str->append(today()->format('d-M-Y')),
                fn (Stringable $str) => $str->append($balance->recorded_until->format('d-M-Y'))
            )
            ->append('.csv')
            ->toString();
    }
}
