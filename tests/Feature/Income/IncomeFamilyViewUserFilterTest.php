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

    $this->income2 = Income::factory()->for(User::factory())->today()->create([
        'description' => 'User 2 Income',
    ]);

    PanelId::FAMILY->setCurrentPanel();
});

it('can display user filter', function () {
    livewire(ListIncomes::class)
        ->assertTableFilterVisible('user');
});

it('can display user columns', function () {
    livewire(ListIncomes::class)
        ->assertTableColumnVisible('user.name');
});

it('display user columns', function () {
    livewire(ListIncomes::class)
        ->assertSee([$this->income1->description])
        ->assertSee([$this->income2->description]);
});
