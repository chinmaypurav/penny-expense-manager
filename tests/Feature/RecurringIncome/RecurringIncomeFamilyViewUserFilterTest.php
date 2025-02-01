<?php

use App\Enums\PanelId;
use App\Filament\Resources\RecurringIncomeResource\Pages\ListRecurringIncomes;
use App\Models\RecurringIncome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->recurringIncome1 = RecurringIncome::factory()->for($this->user)->create([
        'description' => 'User 1 RecurringIncome',
    ]);

    $this->recurringIncome2 = RecurringIncome::factory()->for(User::factory())->create([
        'description' => 'User 2 RecurringIncome',
    ]);

    PanelId::FAMILY->setCurrentPanel();
});

it('can display user filter', function () {
    livewire(ListRecurringIncomes::class)
        ->assertTableFilterVisible('user');
});

it('can display user columns', function () {
    livewire(ListRecurringIncomes::class)
        ->assertTableColumnVisible('user.name');
});

it('display user columns', function () {
    livewire(ListRecurringIncomes::class)
        ->assertSee([$this->recurringIncome1->description])
        ->assertSee([$this->recurringIncome2->description]);
});
