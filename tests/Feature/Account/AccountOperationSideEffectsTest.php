<?php

use App\Filament\Resources\AccountResource\Pages\CreateAccount;
use App\Filament\Resources\AccountResource\Pages\EditAccount;
use App\Models\Account;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Livewire\livewire;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('creates balance initial entry when created', function () {
    $newData = Account::factory()->make();

    livewire(CreateAccount::class)
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

it('updates initial balance date when initial date updated', function () {
    $account = Account::factory()->for($this->user)->today()->createQuietly();
    $balance = Balance::factory()->for($account)->initialRecord()->today()->createQuietly();

    $this->assertDatabaseHas(Balance::class, [
        'id' => $balance->id,
        'account_id' => $account->id,
        'recorded_until' => Carbon::today(),
    ]);

    livewire(EditAccount::class, [
        'record' => $account->getRouteKey(),
    ])
        ->fillForm([
            'initial_date' => Carbon::yesterday(),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Balance::class, [
        'id' => $balance->id,
        'account_id' => $account->id,
        'recorded_until' => Carbon::yesterday(),
    ]);
});
