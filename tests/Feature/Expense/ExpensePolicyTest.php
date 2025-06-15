<?php

use App\Filament\Resources\ExpenseResource;
use App\Filament\Resources\ExpenseResource\Pages\EditExpense;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Expense::factory()->for($this->user)->today()->create([
        'description' => 'User 1 Expense',
    ]);

    Expense::factory()->for(User::factory())->today()->create([
        'description' => 'User 2 Expense',
    ]);
});

it('can render expenses list page with only current user expenses', function () {

    $this->get(ExpenseResource::getUrl('index'))
        ->assertSuccessful()
        ->assertSee('User 1 Expense')
        ->assertDontSee('User 2 Expense');
});

it('cannot render expense edit page for another user', function () {
    $this->get(ExpenseResource::getUrl('edit', [
        'record' => Expense::factory()->create(),
    ]))->assertForbidden();
});

it('cannot retrieve expense data for another user', function () {
    $expense = Expense::factory()->create();

    livewire(EditExpense::class, [
        'record' => $expense->getRouteKey(),
    ])
        ->assertForbidden();
});
