<?php

use App\Enums\PanelId;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\ExpenseChart;
use App\Filament\Widgets\IncomeChart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    PanelId::APP->setCurrentPanel();
});

it('does not show user filter', function () {
    livewire(Dashboard::class)->assertDontSeeText('Users');
});

it('displays income and expense charts', function () {
    $this->get(Dashboard::getUrl())
        ->assertSeeLivewire(IncomeChart::class)
        ->assertSeeLivewire(ExpenseChart::class);
});
