<?php

use App\Enums\PanelId;
use App\Filament\Resources\RecurringExpenseResource;
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

    PanelId::FAMILY->setCurrentPanel();
});

it('cannot display create action', function () {
    livewire(ListRecurringExpenses::class)
        ->assertActionHidden('create');
});

it('cannot display edit action', function () {
    livewire(ListRecurringExpenses::class)
        ->assertTableActionHidden('edit', $this->recurringExpense->id);
});

it('cannot display delete action', function () {
    livewire(ListRecurringExpenses::class)
        ->assertTableActionHidden('delete', $this->recurringExpense->id);
});

it('cannot display import action', function () {
    livewire(ListRecurringExpenses::class)
        ->assertTableActionHidden('delete', $this->recurringExpense->id);
});

it('cannot display bulk delete action', function () {
    livewire(ListRecurringExpenses::class)
        ->set('selectedTableRecords', [$this->recurringExpense])
        ->assertTableBulkActionHidden('delete');
});

it('cannot render create recurringExpense page', function () {
    $this->get(RecurringExpenseResource::getUrl('create'))
        ->assertForbidden();
});

it('cannot perform recurringExpense update action', function () {
    livewire(RecurringExpenseResource\Pages\EditRecurringExpense::class, [
        'record' => $this->recurringExpense->getRouteKey(),
    ])
        ->assertForbidden();
});

it('cannot perform delete recurringExpense action', function () {
    livewire(RecurringExpenseResource\Pages\EditRecurringExpense::class, [
        'record' => $this->recurringExpense->getRouteKey(),
    ])
        ->assertForbidden();
});
