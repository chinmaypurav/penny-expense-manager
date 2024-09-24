<?php

namespace Tests\Jobs;

use App\Jobs\TransactRecurringIncomeJob;
use App\Models\Income;
use App\Models\RecurringIncome;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function PHPUnit\Framework\assertEquals;

uses(DatabaseMigrations::class);

test('it copies recurring income into income', function () {
    $recurringIncome = RecurringIncome::factory()->create();

    (new TransactRecurringIncomeJob($recurringIncome->frequency))->handle();

    assertDatabaseHas(Income::class, [
        'description' => $recurringIncome->description,
        'amount' => $recurringIncome->amount,
        'account_id' => $recurringIncome->account_id,
        'person_id' => $recurringIncome->person_id,
        'category_id' => $recurringIncome->category_id,
        'user_id' => $recurringIncome->user_id,
        'transacted_at' => $recurringIncome->next_transaction_at,
    ]);
});

test('it copies recurring income tags to income', function () {
    $tags = Tag::factory()->count(3)->create();

    $recurringIncome = RecurringIncome::factory()->create();
    $recurringIncome->tags()->attach($tags);

    (new TransactRecurringIncomeJob($recurringIncome->frequency))->handle();

    $incomeTags = Income::first()->tags->pluck('id')->toArray();

    assertEquals($tags->pluck('id')->toArray(), $incomeTags);

});

test('it decrements remaining_recurrences by 1', function () {
    $recurringIncome = RecurringIncome::factory()->create([
        'remaining_recurrences' => fake()->numberBetween(2, 10),
    ]);

    $job = new TransactRecurringIncomeJob($recurringIncome->frequency);

    $job->handle();

    assertDatabaseHas(RecurringIncome::class, [
        'id' => $recurringIncome->id,
        'remaining_recurrences' => $recurringIncome->remaining_recurrences - 1,
    ]);

});

test('it keeps remaining_recurrences null when null remaining_recurrences', function () {
    $recurringIncome = RecurringIncome::factory()->create([
        'remaining_recurrences' => null,
    ]);

    $job = new TransactRecurringIncomeJob($recurringIncome->frequency);

    $job->handle();

    assertDatabaseHas(RecurringIncome::class, [
        'id' => $recurringIncome->id,
        'remaining_recurrences' => null,
    ]);

});

test('it deletes remaining_recurrences remaining_recurrences is 1', function () {
    $recurringIncome = RecurringIncome::factory()->create([
        'remaining_recurrences' => 1,
    ]);

    $job = new TransactRecurringIncomeJob($recurringIncome->frequency);

    $job->handle();

    assertDatabaseMissing(RecurringIncome::class, [
        'id' => $recurringIncome->id,
    ]);

});