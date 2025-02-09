<?php

use App\Enums\PanelId;
use App\Filament\Resources\IncomeResource;
use App\Filament\Resources\IncomeResource\Pages\ListIncomes;
use App\Models\Account;
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

    PanelId::FAMILY->setCurrentPanel();
});

it('cannot display create action', function () {
    livewire(ListIncomes::class)
        ->assertActionHidden('create');
});

it('cannot display edit action', function () {
    livewire(ListIncomes::class)
        ->assertTableActionHidden('edit', $this->income->id);
});

it('cannot display delete action', function () {
    livewire(ListIncomes::class)
        ->assertTableActionHidden('delete', $this->income->id);
});

it('cannot display import action', function () {
    livewire(ListIncomes::class)
        ->assertActionHidden('import');
});

it('cannot display bulk delete action', function () {
    livewire(ListIncomes::class)
        ->set('selectedTableRecords', [$this->income])
        ->assertTableBulkActionHidden('delete');
});

it('cannot render create income page', function () {
    $this->get(IncomeResource::getUrl('create'))
        ->assertForbidden();
});

it('cannot perform income update action', function () {
    livewire(IncomeResource\Pages\EditIncome::class, [
        'record' => $this->income->getRouteKey(),
    ])
        ->assertForbidden();
});

it('cannot perform delete income action', function () {
    livewire(IncomeResource\Pages\EditIncome::class, [
        'record' => $this->income->getRouteKey(),
    ])
        ->assertForbidden();
});

it('displays only current user accounts filter', function () {
    $u1a1 = Account::factory()->for($this->user)->create(['name' => 'u1a1']);
    $u2a2 = Account::factory()->for(User::factory())->create(['name' => 'u2a2']);
    livewire(ListIncomes::class)
        ->assertSeeText($u1a1->name)
        ->assertSeeText($u2a2->name);
});
