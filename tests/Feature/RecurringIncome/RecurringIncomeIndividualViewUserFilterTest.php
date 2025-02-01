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

    $this->recurringIncome2 = RecurringIncome::factory(User::factory())->create([
        'description' => 'User 2 RecurringIncome',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('cannot display user filter', function () {
    livewire(ListRecurringIncomes::class)
        ->assertTableFilterHidden('user');
});

it('cannot display user columns', function () {
    livewire(ListRecurringIncomes::class)
        ->assertTableColumnHidden('user.name');
});

it('can only list auth user recurringIncomes', function () {
    livewire(ListRecurringIncomes::class)
        ->assertSee([$this->recurringIncome1->description])
        ->assertDontSee([$this->recurringIncome2->description]);
});
