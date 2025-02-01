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

    $this->recurringIncome = RecurringIncome::factory()->for($this->user)->create([
        'description' => 'User 1 RecurringIncome',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('can display create action', function () {
    livewire(ListRecurringIncomes::class)
        ->assertActionVisible('create');
});

it('can display edit action', function () {
    livewire(ListRecurringIncomes::class)
        ->assertTableActionVisible('edit', $this->recurringIncome->id);
});

it('can display delete action', function () {
    livewire(ListRecurringIncomes::class)
        ->assertTableActionVisible('delete', $this->recurringIncome->id);
});

it('can display bulk delete action', function () {
    livewire(ListRecurringIncomes::class)
        ->set('selectedTableRecords', [$this->recurringIncome])
        ->assertTableBulkActionVisible('delete');
});
