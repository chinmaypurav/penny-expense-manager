<?php

use App\Enums\PanelId;
use App\Filament\Resources\ExpenseResource\Pages\ListExpenses;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->expense1 = Expense::factory()->for($this->user)->today()->create([
        'description' => 'User 1 Expense',
    ]);

    $this->expense2 = Expense::factory()->for(User::factory())->today()->create([
        'description' => 'User 2 Expense',
    ]);

    PanelId::FAMILY->setCurrentPanel();
});

it('can display user filter', function () {
    livewire(ListExpenses::class)
        ->assertTableFilterVisible('user');
});

it('can display user columns', function () {
    livewire(ListExpenses::class)
        ->assertTableColumnVisible('user.name');
});

it('display user columns', function () {
    livewire(ListExpenses::class)
        ->assertSee([$this->expense1->description])
        ->assertSee([$this->expense2->description]);
});
