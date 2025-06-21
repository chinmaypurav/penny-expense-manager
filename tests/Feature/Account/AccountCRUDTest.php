<?php

use App\Filament\Resources\AccountResource;
use App\Filament\Resources\AccountResource\Pages\CreateAccount;
use App\Filament\Resources\AccountResource\Pages\EditAccount;
use App\Models\Account;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render accounts list page', function () {
    $this->get(AccountResource::getUrl('index'))->assertSuccessful();
});

it('can create account', function () {
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

    $this->assertDatabaseHas(Account::class, [
        'name' => $newData->name,
        'account_type' => $newData->account_type,
    ]);
});

it('can render account edit page', function () {
    $this->get(AccountResource::getUrl('edit', [
        'record' => Account::factory()->for($this->user)->create(),
    ]))->assertSuccessful();
});

it('can retrieve account data', function () {
    $account = Account::factory()->for($this->user)->create();

    livewire(EditAccount::class, [
        'record' => $account->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $account->name,
            'initial_balance' => $account->initial_balance,
            'current_balance' => $account->current_balance,
            'initial_date' => $account->initial_date->toDateString(),
        ]);
});

it('can update account', function () {
    $account = Account::factory()->for($this->user)->create();
    $newData = Account::factory()->make();

    livewire(EditAccount::class, [
        'record' => $account->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newData->name,
            'current_balance' => $newData->current_balance,
            'initial_balance' => $newData->initial_balance,
            'initial_date' => $newData->initial_date,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Account::class, [
        'name' => $newData->name,
        'current_balance' => $newData->current_balance,
    ]);
});

it('can soft delete account', function () {
    $account = Account::factory()->for($this->user)->create();

    livewire(EditAccount::class, [
        'record' => $account->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertSoftDeleted($account);
});
