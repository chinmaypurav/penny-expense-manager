<?php

use App\Enums\PanelId;
use App\Filament\Resources\IncomeResource\Pages\ListIncomes;
use App\Models\Account;
use App\Models\Income;
use App\Models\User;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->income = Income::factory()->for($this->user)->today()->create([
        'description' => 'User 1 Income',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('can display create action', function () {
    livewire(ListIncomes::class)
        ->assertActionVisible('create');
});

it('can display replicate action', function () {
    livewire(ListIncomes::class)
        ->assertTableActionVisible('replicate', $this->income->id);
});

it('can display edit action', function () {
    livewire(ListIncomes::class)
        ->assertTableActionVisible('edit', $this->income->id);
});

it('can display delete action', function () {
    livewire(ListIncomes::class)
        ->assertTableActionVisible('delete', $this->income->id);
});

it('can display import action', function () {
    livewire(ListIncomes::class)
        ->assertActionVisible('import');
});

it('can display bulk delete action', function () {
    livewire(ListIncomes::class)
        ->set('selectedTableRecords', [$this->income])
        ->assertTableBulkActionVisible('delete');
});

it('displays only current user accounts filter', function () {
    $u1a1 = Account::factory()->for($this->user)->create(['name' => 'u1a1']);
    Account::factory()->for(User::factory())->create(['name' => 'u2a2']);

    livewire(ListIncomes::class)
        ->assertTableFilterExists(
            'account_id',
            fn (SelectFilter $filter) => $filter->getLabel() === 'Account'
             && $filter->getOptions() === [$u1a1->id => $u1a1->name]
        );
});

it('displays category filter', function () {
    livewire(ListIncomes::class)
        ->assertTableFilterExists('category_id', fn (SelectFilter $filter) => $filter->getLabel() === 'Category');
});
