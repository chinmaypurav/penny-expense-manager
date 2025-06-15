<?php

use App\Enums\PanelId;
use App\Filament\Resources\IncomeResource\Pages\ListIncomes;
use App\Models\Income;
use App\Models\User;
use Filament\Actions\ReplicateAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    PanelId::APP->setCurrentPanel();
});

it('replicates existing income with current timestamp', function () {
    $income = Income::factory()->for($this->user)->today()->create();
    $this->assertDatabaseCount(Income::class, 1);

    $this->travel(1)->minute();

    livewire(ListIncomes::class)
        ->callTableAction(ReplicateAction::class, $income);

    $expected = $income->replicate([
        'id', 'transacted_at',
    ])->withoutRelations()->toArray();
    $expected['transacted_at'] = now();

    $this->assertDatabaseCount(Income::class, 2);
    $this->assertDatabaseHas(Income::class, $expected);
});
