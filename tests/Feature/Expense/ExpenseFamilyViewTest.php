<?php

use App\Enums\PanelId;
use App\Filament\Resources\ExpenseResource;
use App\Filament\Resources\ExpenseResource\Pages\ListExpenses;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->expense = Expense::factory()->for($this->user)->today()->create([
        'description' => 'User 1 Expense',
    ]);

    PanelId::FAMILY->setCurrentPanel();
});

it('cannot display create action', function () {
    livewire(ListExpenses::class)
        ->assertActionHidden('create');
});

it('cannot display edit action', function () {
    livewire(ListExpenses::class)
        ->assertTableActionHidden('edit', $this->expense->id);
});

it('cannot display delete action', function () {
    livewire(ListExpenses::class)
        ->assertTableActionHidden('delete', $this->expense->id);
});

it('cannot display import action', function () {
    livewire(ListExpenses::class)
        ->assertActionHidden('import');
});

it('cannot display bulk delete action', function () {
    livewire(ListExpenses::class)
        ->set('selectedTableRecords', [$this->expense])
        ->assertTableBulkActionHidden('delete');
});

it('cannot render create expense page', function () {
    $this->get(ExpenseResource::getUrl('create'))
        ->assertForbidden();
});

it('cannot perform expense update action', function () {
    livewire(ExpenseResource\Pages\EditExpense::class, [
        'record' => $this->expense->getRouteKey(),
    ])
        ->assertForbidden();
});

it('cannot perform delete expense action', function () {
    livewire(ExpenseResource\Pages\EditExpense::class, [
        'record' => $this->expense->getRouteKey(),
    ])
        ->assertForbidden();
});
