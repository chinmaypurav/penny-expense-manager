<?php

use App\Enums\PanelId;
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

    PanelId::APP->setCurrentPanel();
});

it('can display account type filter', function () {
    livewire(ListAccounts::class)
        ->assertTableFilterExists('account_type', fn (SelectFilter $selectFilter) => $selectFilter->isMultiple());
});

it('can display create action', function () {
    livewire(ListAccounts::class)
        ->assertActionVisible('create');
});

it('can display edit action', function () {
    livewire(ListAccounts::class)
        ->assertTableActionVisible('edit', $this->account->id);
});

it('can display delete action', function () {
    livewire(ListAccounts::class)
        ->assertTableActionVisible('delete', $this->account->id);
});

it('can display import action', function () {
    livewire(ListAccounts::class)
        ->assertActionVisible('import');
});

it('can display bulk delete action', function () {
    livewire(ListAccounts::class)
        ->set('selectedTableRecords', [$this->account])
        ->assertTableBulkActionVisible('delete');
});
