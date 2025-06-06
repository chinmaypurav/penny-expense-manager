<?php

namespace Tests\Jobs;

use App\Enums\Frequency;
use App\Jobs\TransactRecurringTransferJob;
use App\Models\RecurringTransfer;
use App\Models\Tag;
use App\Models\Transfer;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function PHPUnit\Framework\assertEquals;

uses(DatabaseMigrations::class);

test('it copies recurring transfer into transfer', function () {
    $recurringTransfer = RecurringTransfer::factory()->create();

    new TransactRecurringTransferJob($recurringTransfer)->handle();

    assertDatabaseHas(Transfer::class, [
        'description' => $recurringTransfer->description,
        'amount' => $recurringTransfer->amount,
        'creditor_id' => $recurringTransfer->creditor_id,
        'debtor_id' => $recurringTransfer->debtor_id,
        'user_id' => $recurringTransfer->user_id,
        'transacted_at' => $recurringTransfer->next_transaction_at,
    ]);
});

test('it copies recurring transfer tags to transfer', function () {
    $tags = Tag::factory()->count(3)->create();

    $recurringTransfer = RecurringTransfer::factory()->create();
    $recurringTransfer->tags()->attach($tags);

    new TransactRecurringTransferJob($recurringTransfer)->handle();

    $transferTags = Transfer::first()->tags->pluck('id')->toArray();

    assertEquals($tags->pluck('id')->toArray(), $transferTags);

});

test('it decrements remaining_recurrences by 1', function () {
    $recurringTransfer = RecurringTransfer::factory()->create([
        'remaining_recurrences' => $count = fake()->numberBetween(2, 10),
    ]);

    $job = new TransactRecurringTransferJob($recurringTransfer);

    $job->handle();

    assertDatabaseHas(RecurringTransfer::class, [
        'id' => $recurringTransfer->id,
        'remaining_recurrences' => $count - 1,
    ]);

});

test('it keeps remaining_recurrences null when null remaining_recurrences', function () {
    $recurringTransfer = RecurringTransfer::factory()->create([
        'remaining_recurrences' => null,
    ]);

    $job = new TransactRecurringTransferJob($recurringTransfer);

    $job->handle();

    assertDatabaseHas(RecurringTransfer::class, [
        'id' => $recurringTransfer->id,
        'remaining_recurrences' => null,
    ]);

});

test('it deletes remaining_recurrences when remaining_recurrences is 1', function () {
    $recurringTransfer = RecurringTransfer::factory()->create([
        'remaining_recurrences' => 1,
    ]);

    $job = new TransactRecurringTransferJob($recurringTransfer);

    $job->handle();

    assertDatabaseMissing(RecurringTransfer::class, [
        'id' => $recurringTransfer->id,
    ]);

});

it('updates next_transaction_at when transacting', function () {
    $recurringTransfer = RecurringTransfer::factory()->create([
        'remaining_recurrences' => fake()->numberBetween(2, 10),
        'frequency' => Frequency::MONTHLY,
    ]);

    $nextTransactionAt = $recurringTransfer->next_transaction_at->copy();

    $job = new TransactRecurringTransferJob($recurringTransfer);

    $job->handle();

    assertDatabaseHas(RecurringTransfer::class, [
        'id' => $recurringTransfer->id,
        'next_transaction_at' => $nextTransactionAt->addMonth(),
    ]);
});
