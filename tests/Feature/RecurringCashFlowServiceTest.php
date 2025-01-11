<?php

use App\Enums\Frequency;
use App\Models\Account;
use App\Models\RecurringExpense;
use App\Models\RecurringIncome;
use App\Models\User;
use App\Services\RecurringCashFlowService;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function PHPUnit\Framework\assertEquals;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('it returns recurring incomes total', function () {
    $account = Account::factory()->create();

    RecurringIncome::factory()
        ->for($this->user)
        ->for($account)
        ->create([
            'amount' => 1000,
            'frequency' => Frequency::MONTHLY,
            'remaining_recurrences' => 12,
            'next_transaction_at' => today()->addDay(),
        ]);

    $total = RecurringCashFlowService::processRecurringIncomes(
        $this->user,
        today(),
        today()->addYear()
    );

    assertEquals(12000, $total);
});

test('it returns recurring expenses total', function () {
    $account = Account::factory()->create();

    RecurringExpense::factory()
        ->for($this->user)
        ->for($account)
        ->create([
            'amount' => 1000,
            'frequency' => Frequency::MONTHLY,
            'remaining_recurrences' => 12,
            'next_transaction_at' => today()->addDay(),
        ]);

    $total = RecurringCashFlowService::processRecurringExpenses(
        $this->user,
        today(),
        today()->addYear()
    );

    assertEquals(12000, $total);
});
