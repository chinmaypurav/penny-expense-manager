<?php

namespace Tests\Jobs;

use App\Jobs\TransactRecurringExpenseJob;
use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function PHPUnit\Framework\assertEquals;

uses(DatabaseMigrations::class);

test('it copies recurring expense into expense', function () {
    $recurringExpense = RecurringExpense::factory()->create();

    new TransactRecurringExpenseJob($recurringExpense)->handle();

    assertDatabaseHas(Expense::class, [
        'description' => $recurringExpense->description,
        'amount' => $recurringExpense->amount,
        'account_id' => $recurringExpense->account_id,
        'person_id' => $recurringExpense->person_id,
        'category_id' => $recurringExpense->category_id,
        'user_id' => $recurringExpense->user_id,
        'transacted_at' => $recurringExpense->next_transaction_at,
    ]);
});

test('it does not copy recurring expense into expense when account null', function () {
    $recurringExpense = RecurringExpense::factory()->withoutAccount()->create([
        'remaining_recurrences' => 2,
    ]);

    new TransactRecurringExpenseJob($recurringExpense)->handle();

    assertDatabaseEmpty(Expense::class);

    $recurringExpense->refresh();

    assertEquals(1, $recurringExpense->remaining_recurrences);
});

test('it copies recurring expense tags to expense', function () {
    $tags = Tag::factory()->count(3)->create();

    $recurringExpense = RecurringExpense::factory()->create();
    $recurringExpense->tags()->attach($tags);

    new TransactRecurringExpenseJob($recurringExpense)->handle();

    $expenseTags = Expense::first()->tags->pluck('id')->toArray();

    assertEquals($tags->pluck('id')->toArray(), $expenseTags);

});

test('it decrements remaining_recurrences by 1', function () {
    $recurringExpense = RecurringExpense::factory()->create([
        'remaining_recurrences' => fake()->numberBetween(2, 10),
    ]);

    $job = new TransactRecurringExpenseJob($recurringExpense);

    $job->handle();

    assertDatabaseHas(RecurringExpense::class, [
        'id' => $recurringExpense->id,
        'remaining_recurrences' => $recurringExpense->remaining_recurrences - 1,
    ]);

});

test('it keeps remaining_recurrences null when null remaining_recurrences', function () {
    $recurringExpense = RecurringExpense::factory()->create([
        'remaining_recurrences' => null,
    ]);

    $job = new TransactRecurringExpenseJob($recurringExpense);

    $job->handle();

    assertDatabaseHas(RecurringExpense::class, [
        'id' => $recurringExpense->id,
        'remaining_recurrences' => null,
    ]);

});

test('it deletes remaining_recurrences remaining_recurrences is 1', function () {
    $recurringExpense = RecurringExpense::factory()->create([
        'remaining_recurrences' => 1,
    ]);

    $job = new TransactRecurringExpenseJob($recurringExpense);

    $job->handle();

    assertDatabaseMissing(RecurringExpense::class, [
        'id' => $recurringExpense->id,
    ]);

});
