<?php

use App\Enums\PanelId;
use App\Filament\Resources\ExpenseResource\Pages\ListExpenses;
use App\Models\Expense;
use App\Models\User;
use Filament\Tables\Actions\ReplicateAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    PanelId::APP->setCurrentPanel();
});

it('replicates existing expense with current timestamp', function () {
    $expense = Expense::factory()->for($this->user)->today()->create();
    $this->assertDatabaseCount(Expense::class, 1);

    $this->travel(1)->minute();

    livewire(ListExpenses::class)
        ->callTableAction(ReplicateAction::class, $expense);

    $expected = $expense->replicate([
        'id', 'transacted_at',
    ])->withoutRelations()->toArray();
    $expected['transacted_at'] = now();

    $this->assertDatabaseCount(Expense::class, 2);
    $this->assertDatabaseHas(Expense::class, $expected);
});
