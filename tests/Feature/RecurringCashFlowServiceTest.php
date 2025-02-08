<?php

use App\Enums\Frequency;
use App\Enums\PanelId;
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
    $userAccount = Account::factory()->for($this->user)->create();
    RecurringIncome::factory()
        ->for($this->user)
        ->for($userAccount)
        ->create([
            'amount' => 1000,
            'frequency' => Frequency::MONTHLY,
            'remaining_recurrences' => 12,
            'next_transaction_at' => today()->addDay(),
        ]);

    $familyMember = User::factory()->create();
    $familyMemberAccount = Account::factory()->for($familyMember)->create();
    RecurringIncome::factory()
        ->for($familyMember)
        ->for($familyMemberAccount)
        ->create([
            'amount' => 2000,
            'frequency' => Frequency::MONTHLY,
            'remaining_recurrences' => 12,
            'next_transaction_at' => today()->addDay(),
        ]);
});

test('it returns recurring incomes total only for auth user', function () {
    PanelId::APP->setCurrentPanel();

    $total = RecurringCashFlowService::processRecurringIncomes(
        $this->user,
        today(),
        today()->addYear()
    );

    assertEquals(12000, $total);
});

test('it returns recurring incomes total only for all users', function () {
    PanelId::FAMILY->setCurrentPanel();

    $total = RecurringCashFlowService::processRecurringIncomes(
        $this->user,
        today(),
        today()->addYear()
    );

    assertEquals(36000, $total);
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
