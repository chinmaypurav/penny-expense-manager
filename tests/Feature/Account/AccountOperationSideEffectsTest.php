<?php

use App\Filament\Resources\AccountResource\Pages\CreateAccount;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Livewire\livewire;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('sets current_balance value with initial balance', function () {
    $newData = Account::factory()->make();

    livewire(CreateAccount::class)
        ->fillForm([
            'name' => $newData->name,
            'account_type' => $newData->account_type,
            'initial_balance' => $newData->initial_balance,
            'initial_date' => $newData->initial_date,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $account = Account::latest()->first();

    expect($account->current_balance)->toEqual($newData->initial_balance);
});
