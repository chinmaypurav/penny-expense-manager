<?php

use App\Enums\PanelId;
use App\Filament\Resources\IncomeResource\Pages\ListIncomes;
use App\Models\Income;
use App\Models\User;
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

    $income = Income::factory()->for($this->user)->create([
        'transacted_at' => now()->subMonth(),
    ]);

    assertDatabaseCount(Income::class, 1);

    livewire(ListIncomes::class)
        ->callTableAction('replicate', $income);

    $expected = $income->replicate([
        'id', 'transacted_at',
    ])->withoutRelations()->toArray();
    $expected['transacted_at'] = now();

    assertDatabaseCount(Income::class, 2);
    assertDatabaseHas(Income::class, $expected);
});
