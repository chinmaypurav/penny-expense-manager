<?php

use App\Filament\Widgets\ExpenseChart;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('renders chart label', function () {
    livewire(ExpenseChart::class)
        ->assertSee('Expenses');
});
