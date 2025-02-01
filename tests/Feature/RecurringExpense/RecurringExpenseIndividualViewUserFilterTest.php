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

    $this->recurringExpense1 = RecurringExpense::factory()->for($this->user)->create([
        'description' => 'User 1 RecurringExpense',
    ]);

    $this->recurringExpense2 = RecurringExpense::factory(User::factory())->create([
        'description' => 'User 2 RecurringExpense',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('cannot display user filter', function () {
    livewire(ListRecurringExpenses::class)
        ->assertTableFilterHidden('user');
});

it('cannot display user columns', function () {
    livewire(ListRecurringExpenses::class)
        ->assertTableColumnHidden('user.name');
});

it('can only list auth user recurringExpenses', function () {
    livewire(ListRecurringExpenses::class)
        ->assertSee([$this->recurringExpense1->description])
        ->assertDontSee([$this->recurringExpense2->description]);
});
