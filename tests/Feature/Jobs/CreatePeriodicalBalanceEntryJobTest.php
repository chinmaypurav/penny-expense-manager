<?php

use App\Enums\RecordType;
use App\Jobs\CreatePeriodicalBalanceEntryJob;
use App\Models\Account;
use App\Models\Balance;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

it('creates periodical balance entries', function (
    RecordType $recordType,
    float $balance1,
    float $balance2,
) {
    $account1 = Account::factory()->createQuietly([
        'initial_date' => '2024-01-01',
        'current_balance' => 0,
    ]);
    $account2 = Account::factory()->createQuietly([
        'initial_date' => '2024-01-01',
        'current_balance' => 1000,
    ]);

    /** Account 1 */
    Income::factory()->for($account1)->createManyQuietly([
        ['amount' => 1000, 'transacted_at' => '2025-02-01'], // yearly
        ['amount' => 1000, 'transacted_at' => '2025-03-11'], // monthly | yearly
        ['amount' => 2000, 'transacted_at' => '2025-03-13'], // monthly | yearly
        ['amount' => 4000, 'transacted_at' => '2026-03-15'], // none
    ]);
    Expense::factory()->for($account1)->createManyQuietly([
        ['amount' => 100, 'transacted_at' => '2025-02-01'], // yearly
        ['amount' => 400, 'transacted_at' => '2025-03-10'], // monthly | yearly
        ['amount' => 800, 'transacted_at' => '2025-03-12'], // monthly | yearly
    ]);
    Transfer::factory()->for($account1, 'creditor')->createManyQuietly([
        ['amount' => 300, 'transacted_at' => '2024-12-31'], // none
        ['amount' => 50, 'transacted_at' => '2025-03-01'], // monthly | yearly
        ['amount' => 50, 'transacted_at' => '2026-03-01'], // none
    ]);
    Transfer::factory()->for($account1, 'debtor')->createManyQuietly([
        ['amount' => 300, 'transacted_at' => '2024-12-31'], // none
        ['amount' => 150, 'transacted_at' => '2025-03-01'], // monthly | yearly
        ['amount' => 150, 'transacted_at' => '2026-03-01'], // none
    ]);

    /** Account 2 */
    Income::factory()->for($account2)->createManyQuietly([
        ['amount' => 5000, 'transacted_at' => '2024-02-01'], // none
        ['amount' => 2000, 'transacted_at' => '2025-02-11'], // yearly
        ['amount' => 3000, 'transacted_at' => '2025-03-13'], // monthly | yearly
        ['amount' => 4000, 'transacted_at' => '2026-03-15'], // none
    ]);

    Expense::factory()->for($account2)->createManyQuietly([
        ['amount' => 100, 'transacted_at' => '2024-02-01'], // none
        ['amount' => 300, 'transacted_at' => '2025-03-10'], // monthly | yearly
        ['amount' => 700, 'transacted_at' => '2025-03-12'], // monthly | yearly
        ['amount' => 4000, 'transacted_at' => '2026-03-15'], // none
    ]);
    Transfer::factory()->for($account2, 'creditor')->createManyQuietly([
        ['amount' => 300, 'transacted_at' => '2024-12-31'], // none
        ['amount' => 150, 'transacted_at' => '2025-03-01'], // monthly | yearly
        ['amount' => 150, 'transacted_at' => '2026-03-01'], // none
    ]);
    Transfer::factory()->for($account2, 'debtor')->createManyQuietly([
        ['amount' => 300, 'transacted_at' => '2024-12-31'], // none
        ['amount' => 50, 'transacted_at' => '2025-03-01'], // monthly | yearly
        ['amount' => 50, 'transacted_at' => '2026-03-01'], // none
    ]);

    $this->assertDatabaseEmpty(Balance::class);

    $today = Carbon::parse('2025-03-31');

    CreatePeriodicalBalanceEntryJob::dispatch($recordType, $today);

    $this->assertDatabaseHas(Balance::class, [
        'account_id' => $account1->id,
        'record_type' => $recordType,
        'recorded_until' => Carbon::parse('2025-03-31')->toDateTimeString(),
        'balance' => $balance1,
    ]);

    $this->assertDatabaseHas(Balance::class, [
        'account_id' => $account2->id,
        'record_type' => $recordType,
        'recorded_until' => Carbon::parse('2025-03-31')->toDateTimeString(),
        'balance' => $balance2,
    ]);
})->with([
    'monthly' => [RecordType::MONTHLY, 1700, 3100],
    'yearly' => [RecordType::YEARLY, 2600, 5100],
]);
