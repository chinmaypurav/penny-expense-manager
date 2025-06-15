<?php

use App\Filament\Resources\RecurringExpenseResource;
use App\Filament\Resources\RecurringExpenseResource\Pages\EditRecurringExpense;
use App\Models\RecurringExpense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    RecurringExpense::factory()->for($this->user)->create([
        'description' => 'User 1 Recurring Expense',
    ]);

    RecurringExpense::factory()->for(User::factory())->create([
        'description' => 'User 2 Recurring Expense',
    ]);
});

it('can render recurring expenses list page with only current user recurring expenses', function () {

    $this->get(RecurringExpenseResource::getUrl('index'))
        ->assertSuccessful()
        ->assertSee('User 1 Recurring Expense')
        ->assertDontSee('User 2 Recurring Expense');
});

it('cannot render recurring expense edit page for another user', function () {
    $this->get(RecurringExpenseResource::getUrl('edit', [
        'record' => RecurringExpense::factory()->create(),
    ]))->assertForbidden();
});

it('cannot retrieve recurring expense data for another user', function () {
    $recurringExpense = RecurringExpense::factory()->create();

    livewire(EditRecurringExpense::class, [
        'record' => $recurringExpense->getRouteKey(),
    ])
        ->assertForbidden();
});
