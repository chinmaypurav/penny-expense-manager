<?php

use App\Enums\PanelId;
use App\Filament\Resources\IncomeResource\Pages\ListIncomes;
use App\Models\Income;
use App\Models\User;
use Filament\Tables\Actions\ReplicateAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    PanelId::APP->setCurrentPanel();
});

it('replicates existing income with current timestamp', function () {

    $this->freezeTime();

    $income = Income::factory()->for($this->user)->create([
        'transacted_at' => now()->subDay(),
    ]);

    $this->travel(1)->minute();

    assertDatabaseCount(Income::class, 1);

    livewire(ListIncomes::class)
        ->assertCountTableRecords(1)
        ->callTableAction(ReplicateAction::class, $income)
        ->assertHasNoActionErrors();

    $expected = $income->replicate([
        'id', 'transacted_at',
    ])->withoutRelations()->toArray();
    $expected['transacted_at'] = now()->toDateTimeString();

    assertDatabaseCount(Income::class, 2);
    assertDatabaseHas(Income::class, $expected);
});
