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

    $this->recurringExpense2 = RecurringExpense::factory()->for(User::factory())->create([
        'description' => 'User 2 RecurringExpense',
    ]);

    PanelId::FAMILY->setCurrentPanel();
});

it('can display user filter', function () {
    livewire(ListRecurringExpenses::class)
        ->assertTableFilterVisible('user');
});

it('can display user columns', function () {
    livewire(ListRecurringExpenses::class)
        ->assertTableColumnVisible('user.name');
});

it('display user columns', function () {
    livewire(ListRecurringExpenses::class)
        ->assertSee([$this->recurringExpense1->description])
        ->assertSee([$this->recurringExpense2->description]);
});
