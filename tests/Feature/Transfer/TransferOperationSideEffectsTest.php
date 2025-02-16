<?php

use App\Models\Account;
use App\Models\Balance;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Laravel\travelTo;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('changes account balances on transfer created', function () {
    $ca = Account::factory()->for($this->user)->create(['current_balance' => 1000]);
    $da = Account::factory()->for($this->user)->create(['current_balance' => 2000]);

    Transfer::factory()
        ->for($this->user)
        ->for($da, 'debtor')
        ->for($ca, 'creditor')
        ->create([
            'amount' => 3000,
        ]);

    $this->assertDatabaseHas(Account::class, [
        'id' => $ca->id,
        'current_balance' => 4000,
    ]);
    $this->assertDatabaseHas(Account::class, [
        'id' => $da->id,
        'current_balance' => -1000,
    ]);
});

it('changes account balances on transfer updated', function () {
    $ca = Account::factory()->for($this->user)->create(['current_balance' => 1000]);
    $da = Account::factory()->for($this->user)->create(['current_balance' => 2000]);

    $transfer = Transfer::factory()
        ->for($this->user)
        ->for($da, 'debtor')
        ->for($ca, 'creditor')
        ->today()
        ->createQuietly([
            'amount' => 3000,
        ])->refresh();

    $transfer->update([
        'amount' => 2000, // amount reduced by 2
    ]);

    $this->assertDatabaseHas(Account::class, [
        'id' => $ca->id,
        'current_balance' => 0,
    ]);
    $this->assertDatabaseHas(Account::class, [
        'id' => $da->id,
        'current_balance' => 3000,
    ]);
});

it('changes account balances on transfer deleted', function () {
    $ca = Account::factory()->for($this->user)->create(['current_balance' => 1000]);
    $da = Account::factory()->for($this->user)->create(['current_balance' => 2000]);

    $transfer = Transfer::factory()
        ->for($this->user)
        ->for($da, 'debtor')
        ->for($ca, 'creditor')
        ->today()
        ->createQuietly([
            'amount' => 3000,
        ])->refresh();

    $transfer->delete();

    $this->assertDatabaseHas(Account::class, [
        'id' => $ca->id,
        'current_balance' => -2000,
    ]);
    $this->assertDatabaseHas(Account::class, [
        'id' => $da->id,
        'current_balance' => 5000,
    ]);
});

it('verifies account initial date adjustment when transfer before today added',
    function (
        int $beforeCrDay, int $afterCrDay, int $beforeDrDay, int $afterDrDay
    ) {
        travelTo(Carbon::create(2025, 01, 20));

        $ca = Account::factory()
            ->for($this->user)
            ->create(['current_balance' => 1000, 'initial_date' => Carbon::create(2025, 01, $beforeCrDay)]);
        $da = Account::factory()
            ->for($this->user)
            ->create(['current_balance' => 2000, 'initial_date' => Carbon::create(2025, 01, $beforeDrDay)]);

        Transfer::factory()
            ->for($this->user)
            ->for($da, 'debtor')
            ->for($ca, 'creditor')
            ->create([
                'transacted_at' => Carbon::create(2025, 01, 10),
                'amount' => 3000,
            ]);

        $this->assertDatabaseHas(Account::class, [
            'id' => $ca->id,
            'current_balance' => 4000,
            'initial_date' => Carbon::create(2025, 01, $afterCrDay),
        ]);
        $this->assertDatabaseHas(Balance::class, [
            'account_id' => $ca->id,
            'is_initial_record' => true,
            'recorded_until' => Carbon::create(2025, 01, $afterCrDay),
        ]);
        $this->assertDatabaseHas(Account::class, [
            'id' => $da->id,
            'current_balance' => -1000,
            'initial_date' => Carbon::create(2025, 01, $afterDrDay),
        ]);
        $this->assertDatabaseHas(Balance::class, [
            'account_id' => $da->id,
            'is_initial_record' => true,
            'recorded_until' => Carbon::create(2025, 01, $afterDrDay),
        ]);
    })->with('predated entries');

it('verifies account initial date adjustment when transfer before today updated',
    function (
        int $beforeCrDay, int $afterCrDay, int $beforeDrDay, int $afterDrDay
    ) {
        travelTo($transactedAt = Carbon::create(2025, 01, 20));

        $ca = Account::factory()
            ->for($this->user)
            ->create(['current_balance' => 1000, 'initial_date' => Carbon::create(2025, 01, $beforeCrDay)]);
        $da = Account::factory()
            ->for($this->user)
            ->create(['current_balance' => 2000, 'initial_date' => Carbon::create(2025, 01, $beforeDrDay)]);

        $transfer = Transfer::factory()
            ->for($this->user)
            ->for($da, 'debtor')
            ->for($ca, 'creditor')
            ->today()
            ->createQuietly([
                'transacted_at' => $transactedAt,
                'amount' => 3000,
            ])->refresh();

        $transfer->update([
            'transacted_at' => Carbon::create(2025, 01, 10),
            'amount' => 2000,
        ]);

        $this->assertDatabaseHas(Account::class, [
            'id' => $ca->id,
            'current_balance' => 0,
            // 'initial_date' => Carbon::create(2025, 01, $afterCrDay),
        ]);
        $this->assertDatabaseHas(Balance::class, [
            'account_id' => $ca->id,
            'is_initial_record' => true,
            // 'recorded_until' => Carbon::create(2025, 01, $afterCrDay),
        ]);
        $this->assertDatabaseHas(Account::class, [
            'id' => $da->id,
            'current_balance' => 3000,
            // 'initial_date' => Carbon::create(2025, 01, $afterDrDay),
        ]);
        $this->assertDatabaseHas(Balance::class, [
            'account_id' => $da->id,
            'is_initial_record' => true,
            // 'recorded_until' => Carbon::create(2025, 01, $afterDrDay),
        ]);
    })->with('predated entries');

dataset('predated entries', [
    // before cr day | after cr day | before dr day | after dr day
    'cr before dr before' => [5, 5, 5, 5],
    'cr before dr after' => [5, 5, 15, 10],
    'cr after dr before' => [15, 10, 5, 5],
    'cr after dr after' => [15, 10, 15, 10],
]);
