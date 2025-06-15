<?php

use App\Filament\Widgets\RecurringCashFlowOverviewWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('renders labels on widget', function () {
    $this->actingAs(User::factory()->create());

    livewire(RecurringCashFlowOverviewWidget::class)
        ->assertSee('Total Estimated Incomes')
        ->assertSee('Total Estimated Expenses')
        ->assertSee('Estimated Disposable Income');
});
