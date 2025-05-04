<?php

use App\Console\Commands\Penny\PeriodicBalanceCreateCommand;
use App\Enums\RecordType;
use App\Jobs\CreatePeriodicalBalanceEntryJob;
use App\Models\Account;
use App\Models\Balance;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->travelTo('2025-04-01');
    Queue::fake();
});

it('returns early when project not initialized', function () {
    $this->assertDatabaseEmpty(Account::class);

    $this->artisan(PeriodicBalanceCreateCommand::class)
        ->expectsOutput('No accounts found. Please initialize Penny Project first.')
        ->assertSuccessful();
});

it('dispatches job for given date when no existing entry exists', function (RecordType $recordType) {
    Account::factory()->create(['initial_date' => '2025-01-01']);

    $this->artisan(PeriodicBalanceCreateCommand::class)
        ->expectsQuestion('Record Type: ', $recordType->value)
        ->expectsQuestion('Select month: ', $today = '2025-02-01')
        ->expectsOutput('Balance entry created successfully.')
        ->assertSuccessful();

    Queue::assertPushed(fn (CreatePeriodicalBalanceEntryJob $job) => $job->recordType === $recordType
        && $job->today->is($today)
    );
})->with([
    'monthly' => [RecordType::MONTHLY],
    'yearly' => [RecordType::YEARLY],
]);

it('dispatches job for given date when existing entry exists', function (RecordType $recordType) {
    $account = Account::factory()->createQuietly(['initial_date' => '2025-01-01']);
    Balance::factory()->for($account)->create([
        'recorded_until' => $recordedUntil = '2025-01-31',
        'record_type' => $recordType,
    ]);

    $this->assertDatabaseCount(Balance::class, 1);

    $this->artisan(PeriodicBalanceCreateCommand::class)
        ->expectsQuestion('Record Type: ', $recordType->value)
        ->expectsQuestion('Select month: ', $today = '2025-01-31')
        ->expectsConfirmation('Balance already exists for the selected month. Overwrite?', 'yes')
        ->expectsOutput('Balance entry created successfully.')
        ->assertSuccessful();

    $this->assertDatabaseCount(Balance::class, 0);

    Queue::assertPushed(fn (CreatePeriodicalBalanceEntryJob $job) => $job->recordType === $recordType
        && $job->today->is($today)
    );
})->with([
    'monthly' => [RecordType::MONTHLY],
    'yearly' => [RecordType::YEARLY],
]);

it('does not dispatches job for given date when existing entry exists', function (RecordType $recordType) {
    $account = Account::factory()->create(['initial_date' => '2025-01-01']);
    Balance::factory()->for($account)->create([
        'recorded_until' => '2025-01-31',
        'record_type' => $recordType,
    ]);

    $this->artisan(PeriodicBalanceCreateCommand::class)
        ->expectsQuestion('Record Type: ', $recordType->value)
        ->expectsQuestion('Select month: ', '2025-01-31')
        ->expectsConfirmation('Balance already exists for the selected month. Overwrite?', 'no')
        ->expectsOutput('Balance entry creation cancelled.')
        ->assertSuccessful();

    Queue::assertNotPushed(CreatePeriodicalBalanceEntryJob::class);
})->with([
    'monthly' => [RecordType::MONTHLY],
    'yearly' => [RecordType::YEARLY],
]);
