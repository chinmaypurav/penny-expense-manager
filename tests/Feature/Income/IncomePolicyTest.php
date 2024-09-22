<?php

use App\Filament\Resources\IncomeResource;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Income::factory()->for($this->user)->today()->create([
        'description' => 'User 1 Income',
    ]);

    Income::factory()->for(User::factory())->today()->create([
        'description' => 'User 2 Income',
    ]);
});

it('can render incomes list page with only current user incomes', function () {

    $this->get(IncomeResource::getUrl('index'))
        ->assertSuccessful()
        ->assertSee('User 1 Income')
        ->assertDontSee('User 2 Income');
});

it('cannot render income edit page for another user', function () {
    $this->get(IncomeResource::getUrl('edit', [
        'record' => Income::factory()->create(),
    ]))->assertForbidden();
});

it('cannot retrieve income data for another user', function () {
    $income = Income::factory()->create();

    livewire(IncomeResource\Pages\EditIncome::class, [
        'record' => $income->getRouteKey(),
    ])
        ->assertForbidden();
});
