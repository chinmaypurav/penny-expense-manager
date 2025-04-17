<?php

use App\Jobs\TransactRecurringExpenseJob;
use App\Jobs\TransactRecurringIncomeJob;
use App\Jobs\TransactRecurringTransferJob;
use App\Jobs\TriggerRecurringTransactionsJob;
use App\Models\RecurringExpense;
use App\Models\RecurringIncome;
use App\Models\RecurringTransfer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
});

it('dispatches no jobs when 0 data in recurring tables', function () {
    $this->assertDatabaseEmpty(RecurringIncome::class);
    $this->assertDatabaseEmpty(RecurringExpense::class);
    $this->assertDatabaseEmpty(RecurringTransfer::class);

    $job = new TriggerRecurringTransactionsJob(today());
    $job->handle();

    Queue::assertNothingPushed();
});

it('dispatches recurring income jobs when data in recurring tables', function () {
    RecurringIncome::factory()->create();
    $today = RecurringIncome::factory()->today()->create();

    $job = new TriggerRecurringTransactionsJob(today());
    $job->handle();

    Queue::assertPushed(fn (TransactRecurringIncomeJob $job) => $job->recurringIncome->is($today), 1);
});

it('dispatches recurring expense jobs when data in recurring tables', function () {
    RecurringExpense::factory()->create();
    $today = RecurringExpense::factory()->today()->create();

    $job = new TriggerRecurringTransactionsJob(today());
    $job->handle();

    Queue::assertPushed(fn (TransactRecurringExpenseJob $job) => $job->recurringExpense->is($today), 1);
});

it('dispatches recurring transfer jobs when data in recurring tables', function () {
    RecurringTransfer::factory()->create();
    $today = RecurringTransfer::factory()->today()->create();

    $job = new TriggerRecurringTransactionsJob(today());
    $job->handle();

    Queue::assertPushed(fn (TransactRecurringTransferJob $job) => $job->recurringTransfer->is($today), 1);
});
