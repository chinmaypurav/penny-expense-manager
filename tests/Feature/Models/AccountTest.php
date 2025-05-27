<?php

use App\Models\Account;
use App\Models\Balance;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
use App\Models\User;
use Carbon\CarbonImmutable as Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $account = Account::factory()->has(User::factory())->create();

    expect($account->user)->toBeInstanceOf(User::class);
});

it('has incomes', function () {
    $account = Account::factory()
        ->has(Income::factory()->count(2))
        ->create();

    expect($account->incomes)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Income::class);
});

it('has expenses', function () {
    $account = Account::factory()
        ->has(Expense::factory()->count(2))
        ->create();

    expect($account->expenses)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Expense::class);
});

it('has credit transfers', function () {
    $account = Account::factory()
        ->has(Transfer::factory()->count(2), 'creditTransfers')
        ->create();

    expect($account->creditTransfers)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Transfer::class);
});

it('has debit transfers', function () {
    $account = Account::factory()
        ->has(Transfer::factory()->count(2), 'debitTransfers')
        ->create();

    expect($account->debitTransfers)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Transfer::class);
});

it('has balances', function () {
    $account = Account::factory()
        ->has(Balance::factory()->count(2))
        ->createQuietly();

    expect($account->balances)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Balance::class);
});

it('has previous balance', function () {
    $account = Account::factory()
        ->createQuietly();

    Balance::factory()->for($account)->createQuietly([
        'recorded_until' => Carbon::today()->subDays(2),
    ]);

    $balance = Balance::factory()->for($account)->createQuietly([
        'recorded_until' => Carbon::yesterday(),
    ]);

    Balance::factory()->for($account)->createQuietly([
        'recorded_until' => Carbon::today()->subDays(5),
    ]);

    expect($account->previousBalance)
        ->toBeInstanceOf(Balance::class)
        ->and($account->previousBalance->id)->toBe($balance->id);
});

it('has a label attribute', function () {
    $account = Account::factory()->create();

    expect($account->label)->toBe("{$account->account_type->getShortCode()} | {$account->name} {$account->identifier}");
});
