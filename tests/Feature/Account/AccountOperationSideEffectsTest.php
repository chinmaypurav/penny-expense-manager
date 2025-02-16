<?php

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('creates balance initial entry when created', function () {
    $newData = Account::factory()->make();

    livewire(AccountResource\Pages\CreateAccount::class)
        ->fillForm([
            'name' => $newData->name,
            'account_type' => $newData->account_type,
            'current_balance' => $newData->current_balance,
            'initial_date' => $newData->initial_date,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $account = Account::latest()->first();

    $this->assertDatabaseHas(Balance::class, [
        'account_id' => $account->id,
        'balance' => $newData->current_balance,
        'is_initial_record' => true,
        'recorded_until' => $account->initial_date,
    ]);
});

it('adjusts balance initial entry when account updated', function () {
    $account = Account::factory()->for($this->user)->createQuietly(['current_balance' => 500]);
    $balance = Balance::factory()->for($account)->initialRecord()->createQuietly(['balance' => 100]);

    $newCurrentBalance = 700;

    livewire(AccountResource\Pages\EditAccount::class, [
        'record' => $account->getRouteKey(),
    ])
        ->fillForm([
            'name' => $account->name,
            'current_balance' => $newCurrentBalance,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $diff = $newCurrentBalance - $account->current_balance;

    $newInitialBalance = $balance->balance + $diff;

    $this->assertDatabaseHas(Balance::class, [
        'account_id' => $account->id,
        'balance' => $newInitialBalance,
        'is_initial_record' => true,
    ]);
});

it('updates all balances when account initial date is updated', function () {
    $zeroMonth = today()->subMonths(4);

    $account = Account::factory()
        ->for($this->user)
        ->has(Balance::factory()->count(3)->monthly()->state(new Sequence(
            ['recorded_until' => $firstMonth = today()->subMonths(3)->startOfMonth(), 'balance' => 1000],
            ['recorded_until' => $secondMonth = today()->subMonths(2)->startOfMonth(), 'balance' => 2000],
            ['recorded_until' => $thirdMonth = today()->subMonths(1)->startOfMonth(), 'balance' => 3000],
        )))
        ->createQuietly([
            'initial_date' => $zeroMonth,
            'current_balance' => 0,
        ])->refresh();

    Balance::factory()
        ->for($account)
        ->initialRecord()->state([
            'recorded_until' => $zeroMonth,
            'balance' => 0,
        ])->create();

    $newInitialDate = $zeroMonth->subDay();

    $account->update([
        'initial_date' => $newInitialDate,
        'current_balance' => 1000,
    ]);

    assertDatabaseHas(Balance::class, [
        'account_id' => $account->id,
        'balance' => 1000,
        'is_initial_record' => true,
        'recorded_until' => $newInitialDate,
    ]);
    assertDatabaseHas(Balance::class, [
        'account_id' => $account->id,
        'balance' => 2000,
        'is_initial_record' => false,
        'recorded_until' => $firstMonth,
    ]);
    assertDatabaseHas(Balance::class, [
        'account_id' => $account->id,
        'balance' => 3000,
        'is_initial_record' => false,
        'recorded_until' => $secondMonth,
    ]);
    assertDatabaseHas(Balance::class, [
        'account_id' => $account->id,
        'balance' => 4000,
        'is_initial_record' => false,
        'recorded_until' => $thirdMonth,
    ]);
});
