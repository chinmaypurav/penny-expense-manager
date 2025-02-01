<?php

use App\Enums\PanelId;
use App\Filament\Resources\RecurringIncomeResource;
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

    PanelId::FAMILY->setCurrentPanel();
});

it('cannot display create action', function () {
    livewire(ListRecurringIncomes::class)
        ->assertActionHidden('create');
});

it('cannot display edit action', function () {
    livewire(ListRecurringIncomes::class)
        ->assertTableActionHidden('edit', $this->recurringIncome->id);
});

it('cannot display delete action', function () {
    livewire(ListRecurringIncomes::class)
        ->assertTableActionHidden('delete', $this->recurringIncome->id);
});

it('cannot display import action', function () {
    livewire(ListRecurringIncomes::class)
        ->assertTableActionHidden('delete', $this->recurringIncome->id);
});

it('cannot display bulk delete action', function () {
    livewire(ListRecurringIncomes::class)
        ->set('selectedTableRecords', [$this->recurringIncome])
        ->assertTableBulkActionHidden('delete');
});

it('cannot render create recurringIncome page', function () {
    $this->get(RecurringIncomeResource::getUrl('create'))
        ->assertForbidden();
});

it('cannot perform recurringIncome update action', function () {
    livewire(RecurringIncomeResource\Pages\EditRecurringIncome::class, [
        'record' => $this->recurringIncome->getRouteKey(),
    ])
        ->assertForbidden();
});

it('cannot perform delete recurringIncome action', function () {
    livewire(RecurringIncomeResource\Pages\EditRecurringIncome::class, [
        'record' => $this->recurringIncome->getRouteKey(),
    ])
        ->assertForbidden();
});
