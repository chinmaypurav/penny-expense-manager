<?php

use App\Enums\PanelId;
use App\Filament\Resources\AccountResource;
use App\Filament\Resources\AccountResource\Pages\EditAccount;
use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Models\Account;
use App\Models\User;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->account = Account::factory()->for($this->user)->create([
        'name' => 'User 1 Account',
    ]);

    PanelId::FAMILY->setCurrentPanel();
});

it('can display account type filter', function () {
    livewire(ListAccounts::class)
        ->assertTableFilterExists('account_type', fn (SelectFilter $selectFilter) => $selectFilter->isMultiple());
});

it('cannot display create action', function () {
    livewire(ListAccounts::class)
        ->assertActionHidden('create');
});

it('cannot display edit action', function () {
    livewire(ListAccounts::class)
        ->assertTableActionHidden('edit', $this->account->id);
});

it('cannot display delete action', function () {
    livewire(ListAccounts::class)
        ->assertTableActionHidden('delete', $this->account->id);
});

it('cannot display import action', function () {
    livewire(ListAccounts::class)
        ->assertActionHidden('import');
});

it('cannot display bulk delete action', function () {
    livewire(ListAccounts::class)
        ->set('selectedTableRecords', [$this->account])
        ->assertTableBulkActionHidden('delete');
});

it('cannot render create account page', function () {
    $this->get(AccountResource::getUrl('create'))
        ->assertForbidden();
});

it('cannot perform account update action', function () {
    livewire(EditAccount::class, [
        'record' => $this->account->getRouteKey(),
    ])
        ->assertForbidden();
});

it('cannot perform delete account action', function () {
    livewire(EditAccount::class, [
        'record' => $this->account->getRouteKey(),
    ])
        ->assertForbidden();
});
