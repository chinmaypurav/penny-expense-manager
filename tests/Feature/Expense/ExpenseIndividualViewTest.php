<?php

use App\Enums\PanelId;
use App\Filament\Resources\ExpenseResource\Pages\ListExpenses;
use App\Models\Account;
use App\Models\Expense;
use App\Models\User;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->expense = Expense::factory()->for($this->user)->today()->create([
        'description' => 'User 1 Expense',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('can display create action', function () {
    livewire(ListExpenses::class)
        ->assertActionVisible('create');
});

it('can display edit action', function () {
    livewire(ListExpenses::class)
        ->assertTableActionVisible('edit', $this->expense->id);
});

it('can display delete action', function () {
    livewire(ListExpenses::class)
        ->assertTableActionVisible('delete', $this->expense->id);
});

it('can display import action', function () {
    livewire(ListExpenses::class)
        ->assertActionVisible('import');
});

it('can display bulk delete action', function () {
    livewire(ListExpenses::class)
        ->set('selectedTableRecords', [$this->expense])
        ->assertTableBulkActionVisible('delete');
});

it('displays only current user accounts filter', function () {
    $u1a1 = Account::factory()->for($this->user)->create(['name' => 'u1a1']);
    Account::factory()->for(User::factory())->create(['name' => 'u2a2']);

    livewire(ListExpenses::class)
        ->assertTableFilterExists(
            'account_id',
            fn (SelectFilter $filter) => $filter->getLabel() === 'Account'
                && $filter->getOptions() === [$u1a1->id => $u1a1->name]
        );
});

it('displays category filter', function () {
    livewire(ListExpenses::class)
        ->assertTableFilterExists('category_id', fn (SelectFilter $filter) => $filter->getLabel() === 'Category');
});
