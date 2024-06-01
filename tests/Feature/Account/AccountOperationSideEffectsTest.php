<?php

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Livewire\livewire;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Carbon::setTestNow(today());

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
        ])
        ->call('create')
        ->assertOk();

    $account = Account::latest()->first();

    $this->assertDatabaseHas(Balance::class, [
        'account_id' => $account->id,
        'balance' => $newData->current_balance,
        'is_initial_record' => true,
        'recorded_until' => today()->subDay(),
    ]);
});

it('adjusts balance initial entry when account updated', function () {
    $account = Account::factory()->createQuietly(['current_balance' => 500]);
    $balance = Balance::factory()->for($account)->initialRecord()->createQuietly(['balance' => 100]);

    $newCurrentBalance = 700;

    livewire(AccountResource\Pages\EditAccount::class, [
        'record' => $account->getRouteKey(),
    ])
        ->fillForm([
            'name' => $account->name,
            'account_type' => $account->account_type,
            'current_balance' => $newCurrentBalance,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $diff = $newCurrentBalance - $account->current_balance;

    $newInitialBalance = $balance->balance - $diff;

    $this->assertDatabaseHas(Balance::class, [
        'account_id' => $account->id,
        'balance' => $newInitialBalance,
        'is_initial_record' => true,
    ]);
});
