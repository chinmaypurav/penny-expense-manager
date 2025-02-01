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

    $this->income = Income::factory()->for($this->user)->today()->create([
        'description' => 'User 1 Income',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('can display create action', function () {
    livewire(ListIncomes::class)
        ->assertActionVisible('create');
});

it('can display edit action', function () {
    livewire(ListIncomes::class)
        ->assertTableActionVisible('edit', $this->income->id);
});

it('can display delete action', function () {
    livewire(ListIncomes::class)
        ->assertTableActionVisible('delete', $this->income->id);
});

it('can display import action', function () {
    livewire(ListIncomes::class)
        ->assertActionVisible('import');
});

it('can display bulk delete action', function () {
    livewire(ListIncomes::class)
        ->set('selectedTableRecords', [$this->income])
        ->assertTableBulkActionVisible('delete');
});
