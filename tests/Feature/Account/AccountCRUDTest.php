<?php

use App\Filament\Resources\AccountResource;
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

    livewire(AccountResource\Pages\CreateAccount::class)
        ->fillForm([
            'name' => $newData->name,
            'account_type' => $newData->account_type,
            'current_balance' => $newData->current_balance,
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
        'record' => Account::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve account data', function () {
    $account = Account::factory()->create();

    livewire(AccountResource\Pages\EditAccount::class, [
        'record' => $account->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $account->name,
            'account_type' => $account->account_type->value,
            'current_balance' => $account->current_balance,
            'initial_date' => $account->initial_date,
        ]);
});

it('can update account', function () {
    $account = Account::factory()->create();
    $newData = Account::factory()->make();

    livewire(AccountResource\Pages\EditAccount::class, [
        'record' => $account->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newData->name,
            'account_type' => $newData->account_type,
            'current_balance' => $newData->current_balance,
            'initial_date' => $newData->initial_date,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Account::class, [
        'name' => $newData->name,
        'account_type' => $newData->account_type,
        'current_balance' => $newData->current_balance,
    ]);
});

it('can delete account', function () {
    $account = Account::factory()->for($this->user)->create();

    livewire(AccountResource\Pages\EditAccount::class, [
        'record' => $account->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($account);
});
