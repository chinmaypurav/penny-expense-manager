<?php

use App\Enums\PanelId;
use App\Filament\Pages\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders a navigation menu in app panel', function () {
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
        ])->assertDontSeeText([
            'Users',
        ]);
});

it('renders a navigation menu in family panel', function () {
    PanelId::FAMILY->setCurrentPanel();

    $this->get(Dashboard::getUrl())
        ->assertSuccessful()
        ->assertSeeText([
            'Dashboard',
            'Users',
            'Accounts',
            'Expenses',
            'Incomes',
            'Transfers',
            'Recurring Transactions',
            'Recurring CashFlow Overview',
            'Recurring Expenses',
            'Recurring Incomes',
            'Recurring Transfers',
        ])->assertDontSeeText([
            'Balances',
            'Categories',
            'People',
            'Tags',
        ]);
});
