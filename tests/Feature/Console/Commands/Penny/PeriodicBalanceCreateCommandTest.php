<?php

use App\Console\Commands\Penny\PeriodicBalanceCreateCommand;
use App\Enums\RecordType;
use App\Jobs\CreatePeriodicalBalanceEntryJob;
use App\Models\Account;
use App\Models\Balance;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->travelTo('2025-02-01');
    Queue::fake();
});

dataset('command prompts dataset', function () {
    return [
        'monthly' => [
            RecordType::MONTHLY,
            [
                RecordType::MONTHLY->value,
                RecordType::YEARLY->value,
            ],
            [
                '2024-11-30' => 'Nov-24',
                '2024-12-31' => 'Dec-24',
                '2025-01-31' => 'Jan-25',
            ],
        ],
        'yearly' => [
            RecordType::YEARLY,
            [
                RecordType::MONTHLY->value,
                RecordType::YEARLY->value,
            ],
            [
                '2024-12-31' => 'Dec-24',
            ],
        ],
    ];
});

it('returns early when project not initialized', function () {
    $this->assertDatabaseEmpty(Account::class);

    $this->artisan(PeriodicBalanceCreateCommand::class)
        ->expectsOutput('No accounts found. Please initialize Penny Project first.')
        ->assertSuccessful();
});

it('dispatches job for given date when no existing entry exists',
    function (RecordType $recordType, array $expectedRecordTypes, array $expectedMonths) {
        Account::factory()->create(['initial_date' => '2024-11-01']);

        $this->artisan(PeriodicBalanceCreateCommand::class)
            ->expectsChoice('Record Type: ', $recordType->value, $expectedRecordTypes)
            ->expectsChoice('Select month: ', $today = '2025-02-01', $expectedMonths)
            ->expectsOutput('Balance entry created successfully.')
            ->assertSuccessful();

        Queue::assertPushed(fn (CreatePeriodicalBalanceEntryJob $job) => $job->recordType === $recordType
            && $job->today->is($today)
        );
    })->with('command prompts dataset');

it('dispatches job for given date when existing entry exists',
    function (RecordType $recordType, array $expectedRecordTypes, array $expectedMonths) {
        $account = Account::factory()->createQuietly(['initial_date' => '2024-11-01']);
        Balance::factory()->for($account)->create([
            'recorded_until' => $recordedUntil = '2024-01-31',
            'record_type' => $recordType,
        ]);

        $this->assertDatabaseCount(Balance::class, 1);

        $this->artisan(PeriodicBalanceCreateCommand::class)
            ->expectsChoice('Record Type: ', $recordType->value, $expectedRecordTypes)
            ->expectsChoice('Select month: ', $recordedUntil, $expectedMonths)
            ->expectsConfirmation('Balance already exists for the selected month. Overwrite?', 'yes')
            ->expectsOutput('Balance entry created successfully.')
            ->assertSuccessful();

        $this->assertDatabaseCount(Balance::class, 0);

        Queue::assertPushed(fn (CreatePeriodicalBalanceEntryJob $job) => $job->recordType === $recordType
            && $job->today->is($recordedUntil)
        );
    })->with('command prompts dataset');

it('does not dispatches job for given date when existing entry exists',
    function (RecordType $recordType, array $expectedRecordTypes, array $expectedMonths) {
        $account = Account::factory()->createQuietly(['initial_date' => '2024-11-01']);
        Balance::factory()->for($account)->create([
            'recorded_until' => $recordedUntil = '2024-01-31',
            'record_type' => $recordType,
        ]);

        $this->artisan(PeriodicBalanceCreateCommand::class)
            ->expectsChoice('Record Type: ', $recordType->value, $expectedRecordTypes)
            ->expectsChoice('Select month: ', $recordedUntil, $expectedMonths)
            ->expectsConfirmation('Balance already exists for the selected month. Overwrite?', 'no')
            ->expectsOutput('Balance entry creation cancelled.')
            ->assertSuccessful();

        Queue::assertNotPushed(CreatePeriodicalBalanceEntryJob::class);
    })->with('command prompts dataset');

it('returns early when no choices for date are available',
    function (RecordType $recordType, array $expectedRecordTypes, array $expectedMonths) {
        $this->travelTo('2025-04-01');
        Account::factory()->createQuietly(['initial_date' => '2025-04-01']);

        $this->artisan(PeriodicBalanceCreateCommand::class)
            ->expectsChoice('Record Type: ', $recordType->value, $expectedRecordTypes)
            ->expectsOutput('No account is old enough to create historical balance entries.')
            ->assertSuccessful();

        $this->assertDatabaseCount(Balance::class, 0);
    })->with('command prompts dataset');
