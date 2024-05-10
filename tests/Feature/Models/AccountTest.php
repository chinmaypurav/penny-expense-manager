<?php

use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
use App\Models\User;
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
