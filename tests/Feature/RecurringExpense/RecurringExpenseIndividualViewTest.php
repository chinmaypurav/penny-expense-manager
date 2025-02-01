<?php

use App\Enums\PanelId;
use App\Filament\Resources\RecurringExpenseResource\Pages\ListRecurringExpenses;
use App\Models\RecurringExpense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->recurringExpense = RecurringExpense::factory()->for($this->user)->create([
        'description' => 'User 1 RecurringExpense',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('can display create action', function () {
    livewire(ListRecurringExpenses::class)
        ->assertActionVisible('create');
});

it('can display edit action', function () {
    livewire(ListRecurringExpenses::class)
        ->assertTableActionVisible('edit', $this->recurringExpense->id);
});

it('can display delete action', function () {
    livewire(ListRecurringExpenses::class)
        ->assertTableActionVisible('delete', $this->recurringExpense->id);
});

it('can display bulk delete action', function () {
    livewire(ListRecurringExpenses::class)
        ->set('selectedTableRecords', [$this->recurringExpense])
        ->assertTableBulkActionVisible('delete');
});
