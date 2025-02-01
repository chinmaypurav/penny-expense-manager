<?php

use App\Enums\PanelId;
use App\Filament\Resources\IncomeResource\Pages\ListIncomes;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->income1 = Income::factory()->for($this->user)->today()->create([
        'description' => 'User 1 Income',
    ]);

    $this->income2 = Income::factory(User::factory())->today()->create([
        'description' => 'User 2 Income',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('cannot display user filter', function () {
    livewire(ListIncomes::class)
        ->assertTableFilterHidden('user');
});

it('cannot display user columns', function () {
    livewire(ListIncomes::class)
        ->assertTableColumnHidden('user.name');
});

it('can only list auth user incomes', function () {
    livewire(ListIncomes::class)
        ->assertSee([$this->income1->description])
        ->assertDontSee([$this->income2->description]);
});
