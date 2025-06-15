<?php

use App\Filament\Resources\RecurringIncomeResource;
use App\Filament\Resources\RecurringIncomeResource\Pages\EditRecurringIncome;
use App\Models\RecurringIncome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    RecurringIncome::factory()->for($this->user)->create([
        'description' => 'User 1 Recurring Income',
    ]);

    RecurringIncome::factory()->for(User::factory())->create([
        'description' => 'User 2 Recurring Income',
    ]);
});

it('can render recurring incomes list page with only current user recurring incomes', function () {

    $this->get(RecurringIncomeResource::getUrl('index'))
        ->assertSuccessful()
        ->assertSee('User 1 Recurring Income')
        ->assertDontSee('User 2 Recurring Income');
});

it('cannot render recurring income edit page for another user', function () {
    $this->get(RecurringIncomeResource::getUrl('edit', [
        'record' => RecurringIncome::factory()->create(),
    ]))->assertForbidden();
});

it('cannot retrieve recurring income data for another user', function () {
    $recurringIncome = RecurringIncome::factory()->create();

    livewire(EditRecurringIncome::class, [
        'record' => $recurringIncome->getRouteKey(),
    ])
        ->assertForbidden();
});
