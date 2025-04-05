<?php

use App\Enums\PanelId;
use App\Filament\Pages\Today;
use App\Livewire\TodayExpenses;
use App\Livewire\TodayIncomes;
use App\Livewire\TodayTransfers;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    PanelId::FAMILY->setCurrentPanel();
});

it('displays income expense and transfer tables', function (PanelId $panelId) {
    $panelId->setCurrentPanel();

    Income::factory()->for($this->user)->today()->create();
    Expense::factory()->for($this->user)->today()->create();
    Transfer::factory()->for($this->user)->today()->create();

    $this->get(Today::getUrl())
        ->assertSeeLivewire(TodayIncomes::class)
        ->assertSeeLivewire(TodayExpenses::class)
        ->assertSeeLivewire(TodayTransfers::class);
})->with([
    PanelId::APP,
    PanelId::FAMILY,
]);
