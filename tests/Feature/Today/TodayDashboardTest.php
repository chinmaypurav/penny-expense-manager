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
use Filament\Forms\Components\DatePicker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    PanelId::FAMILY->setCurrentPanel();
});

it('sets the default form value to today', function () {
    livewire(Today::class)
        ->assertFormFieldExists(
            'transacted_at',
            checkFieldUsing: fn (DatePicker $field) => $field->getState() === today()->toDateString()
        );
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
})->with('today dashboard panel ids');

it('displays incomes only for today', function (PanelId $panelId) {
    $panelId->setCurrentPanel();

    Income::factory()->yesterday()->count(2)->create();
    Income::factory()->for($this->user)->today()->create();
    Income::factory()->tomorrow()->count(2)->create();

    livewire(TodayIncomes::class)
        ->assertCountTableRecords(1);

})->with('today dashboard panel ids');

it('displays expenses only for today', function (PanelId $panelId) {
    $panelId->setCurrentPanel();

    Expense::factory()->yesterday()->count(2)->create();
    Expense::factory()->for($this->user)->today()->create();
    Expense::factory()->tomorrow()->count(2)->create();

    livewire(TodayExpenses::class)
        ->assertCountTableRecords(1);

})->with('today dashboard panel ids');

it('displays transfers only for today', function (PanelId $panelId) {
    $panelId->setCurrentPanel();

    Transfer::factory()->yesterday()->count(2)->create();
    Transfer::factory()->for($this->user)->today()->create();
    Transfer::factory()->tomorrow()->count(2)->create();

    livewire(TodayTransfers::class)
        ->assertCountTableRecords(1);

})->with('today dashboard panel ids');

dataset('today dashboard panel ids', [
    PanelId::APP,
    PanelId::FAMILY,
]);
