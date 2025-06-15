<?php

use App\Filament\Widgets\IncomeChart;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('renders chart label', function () {
    livewire(IncomeChart::class)
        ->assertSee('Incomes');
});
