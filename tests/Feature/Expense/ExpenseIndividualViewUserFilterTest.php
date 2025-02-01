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

    $this->expense2 = Expense::factory(User::factory())->today()->create([
        'description' => 'User 2 Expense',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('cannot display user filter', function () {
    livewire(ListExpenses::class)
        ->assertTableFilterHidden('user');
});

it('cannot display user columns', function () {
    livewire(ListExpenses::class)
        ->assertTableColumnHidden('user.name');
});

it('can only list auth user expenses', function () {
    livewire(ListExpenses::class)
        ->assertSee([$this->expense1->description])
        ->assertDontSee([$this->expense2->description]);
});
