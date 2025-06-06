<?php

use App\Enums\PanelId;
use App\Filament\Pages\DayWise;
use App\Livewire\DayWiseExpenses;
use App\Livewire\DayWiseIncomes;
use App\Livewire\DayWiseTransfers;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
use App\Models\User;
use Carbon\CarbonImmutable as Carbon;
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
    livewire(DayWise::class)
        ->assertFormFieldExists(
            'transacted_at',
            checkFieldUsing: fn (DatePicker $field) => $field->getState() === today()->toDateString()
        );
});

it('changes the filters property value when form updated', function () {
    livewire(DayWise::class)
        ->assertSet('filters.transacted_at', today()->toDateString())
        ->fillForm(['transacted_at' => $newTransactedAt = today()->subDay()->toDateString()])
        ->assertSet('filters.transacted_at', $newTransactedAt);
});

it('displays income expense and transfer tables', function (PanelId $panelId) {
    $panelId->setCurrentPanel();

    Income::factory()->for($this->user)->today()->create();
    Expense::factory()->for($this->user)->today()->create();
    Transfer::factory()->for($this->user)->today()->create();

    $this->get(DayWise::getUrl())
        ->assertSeeLivewire(DayWiseIncomes::class)
        ->assertSeeLivewire(DayWiseExpenses::class)
        ->assertSeeLivewire(DayWiseTransfers::class);
})->with('date and panel id dataset for data filter');

it('displays incomes only for passed date', function (PanelId $panelId, array $keys, ?string $transactedAt = null) {
    $this->travelTo('2025-02-04');
    $date = Carbon::create(2025, 2, 2);
    $panelId->setCurrentPanel();

    $user = User::factory()->create();

    Income::factory()->for($this->user)->create(['transacted_at' => $date->subDay(), 'id' => 1]);
    Income::factory()->for($this->user)->create(['transacted_at' => $date, 'id' => 2]);
    Income::factory()->for($user)->create(['transacted_at' => $date, 'id' => 3]);
    Income::factory()->for($user)->create(['transacted_at' => $date->addDay(), 'id' => 4]);
    Income::factory()->for($this->user)->today()->create(['id' => 5]);
    Income::factory()->for($user)->today()->create(['id' => 6]);

    $records = Income::whereKey($keys)->get();

    livewire(DayWiseIncomes::class, ['filters' => ['transacted_at' => $transactedAt]])
        ->assertCountTableRecords($records->count())
        ->assertCanSeeTableRecords($records);
})->with('date and panel id dataset for data filter');

it('displays expenses only passed date', function (PanelId $panelId, array $keys, ?string $transactedAt = null) {
    $this->travelTo('2025-02-04');
    $date = Carbon::create(2025, 2, 2);
    $panelId->setCurrentPanel();

    $user = User::factory()->create();

    Expense::factory()->for($this->user)->create(['transacted_at' => $date->subDay(), 'id' => 1]);
    Expense::factory()->for($this->user)->create(['transacted_at' => $date, 'id' => 2]);
    Expense::factory()->for($user)->create(['transacted_at' => $date, 'id' => 3]);
    Expense::factory()->for($user)->create(['transacted_at' => $date->addDay(), 'id' => 4]);
    Expense::factory()->for($this->user)->today()->create(['id' => 5]);
    Expense::factory()->for($user)->today()->create(['id' => 6]);

    $records = Expense::whereKey($keys)->get();

    livewire(DayWiseExpenses::class, ['filters' => ['transacted_at' => $transactedAt]])
        ->assertCountTableRecords($records->count())
        ->assertCanSeeTableRecords($records);
})->with('date and panel id dataset for data filter');

it('displays transfers only for passed date', function (PanelId $panelId, array $keys, ?string $transactedAt = null) {
    $this->travelTo('2025-02-04');
    $date = Carbon::create(2025, 2, 2);
    $panelId->setCurrentPanel();

    $user = User::factory()->create();

    Transfer::factory()->for($this->user)->create(['transacted_at' => $date->subDay(), 'id' => 1]);
    Transfer::factory()->for($this->user)->create(['transacted_at' => $date, 'id' => 2]);
    Transfer::factory()->for($user)->create(['transacted_at' => $date, 'id' => 3]);
    Transfer::factory()->for($user)->create(['transacted_at' => $date->addDay(), 'id' => 4]);
    Transfer::factory()->for($this->user)->today()->create(['id' => 5]);
    Transfer::factory()->for($user)->today()->create(['id' => 6]);

    $records = Transfer::whereKey($keys)->get();

    livewire(DayWiseTransfers::class, ['filters' => ['transacted_at' => $transactedAt]])
        ->assertCountTableRecords($records->count())
        ->assertCanSeeTableRecords($records);
})->with('date and panel id dataset for data filter');

dataset('date and panel id dataset for data filter', [
    'app panel with date' => [PanelId::APP, [2], '2025-02-02'],
    'app panel without date' => [PanelId::APP, [5]],
    'family panel with date' => [PanelId::FAMILY, [2, 3], '2025-02-02'],
    'family panel without date' => [PanelId::FAMILY, [5, 6]],
]);
