<?php

use App\Filament\Widgets\RecurringCashFlowOverviewWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('sees recurring cash flow overview widget', function () {
    $this->get('/app/recurring/overview')
        ->assertOk()
        ->assertSeeLivewire(RecurringCashFlowOverviewWidget::class);
});
