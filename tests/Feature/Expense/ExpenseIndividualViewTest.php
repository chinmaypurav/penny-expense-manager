<?php

use App\Enums\PanelId;
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
