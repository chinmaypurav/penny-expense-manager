<?php

use App\Enums\RecordType;
use App\Models\Account;
use App\Models\Balance;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
use App\Models\User;
use App\Services\AccountTransactionService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('returns transactions data sorted', function (RecordType $recordType, int $offsetMonths, string $today, string $balanceUntil) {
    $offsetDays = $offsetMonths * 31;
    $this->travelTo($today);
    $account = Account::factory()->for($this->user)->create();
    $balance = Balance::factory()->for($account)->create([
        'record_type' => $recordType,
        'recorded_until' => $recordedUntil = CarbonImmutable::make($balanceUntil),
    ]);

    // pre period - exclude
    Income::factory()->for($account)->for($this->user)->create([
        'transacted_at' => $recordedUntil->subDays($offsetDays),
    ]);
    Expense::factory()->for($account)->for($this->user)->create([
        'transacted_at' => $recordedUntil->subDays($offsetDays),
    ]);
    Transfer::factory()->for($account, 'debtor')->for($this->user)->create([
        'transacted_at' => $recordedUntil->subDays($offsetDays),
    ]);
    Transfer::factory()->for($account, 'creditor')->for($this->user)->create([
        'transacted_at' => $recordedUntil->subDays($offsetDays),
    ]);

    // during period - include
    $order3 = Income::factory()->for($account)->for($this->user)->create([
        'transacted_at' => $recordedUntil->subDays(10),
    ]);
    $order4 = Expense::factory()->for($account)->for($this->user)->create([
        'transacted_at' => $recordedUntil->subDays(2),
    ]);
    $order1 = Transfer::factory()->for($account, 'debtor')->for($this->user)->create([
        'transacted_at' => $recordedUntil->subDays(27),
    ]);
    $order2 = Transfer::factory()->for($account, 'creditor')->for($this->user)->create([
        'transacted_at' => $recordedUntil->subDays(25),
    ]);

    // post period - exclude
    Income::factory()->for($account)->for($this->user)->create([
        'transacted_at' => $recordedUntil->addDays($offsetDays),
    ]);
    Expense::factory()->for($account)->for($this->user)->create([
        'transacted_at' => $recordedUntil->addDays($offsetDays),
    ]);
    Transfer::factory()->for($account, 'debtor')->for($this->user)->create([
        'transacted_at' => $recordedUntil->addDays($offsetDays),
    ]);
    Transfer::factory()->for($account, 'creditor')->for($this->user)->create([
        'transacted_at' => $recordedUntil->addDays($offsetDays),
    ]);

    $transactions = app(AccountTransactionService::class)->getTransactions($balance);

    $this->assertCount(4, $transactions);

    $expected = [$order1->description, $order2->description, $order3->description, $order4->description];
    $actual = $transactions->pluck('description')->values()->all();
    $this->assertEquals($expected, $actual);
})->with([
    'monthly' => [RecordType::MONTHLY, 1, '2025-04-01', '2025-03-31'],
    'yearly' => [RecordType::YEARLY, 12, '2025-04-01', '2024-03-31'],
]);
