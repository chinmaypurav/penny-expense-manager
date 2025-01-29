<?php

use App\Filament\Pages\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders a navigation menu', function () {
    $this->get(Dashboard::getUrl())
        ->assertSuccessful()
        ->assertSeeText([
            'Dashboard',
            'Accounts',
            'Balances',
            'Categories',
            'Expenses',
            'Incomes',
            'People',
            'Tags',
            'Transfers',
            'Recurring Transactions',
            'Recurring CashFlow Overview',
            'Recurring Expenses',
            'Recurring Incomes',
            'Recurring Transfers',
        ]);
});
