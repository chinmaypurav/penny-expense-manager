<?php

use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('has accounts', function () {
    $user = User::factory()
        ->has(Account::factory()->count(2))
        ->create();

    expect($user->accounts)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Account::class);
});

it('has incomes', function () {
    $user = User::factory()
        ->has(Income::factory()->count(2))
        ->create();

    expect($user->incomes)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Income::class);
});

it('has expenses', function () {
    $user = User::factory()
        ->has(Expense::factory()->count(2))
        ->create();

    expect($user->expenses)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Expense::class);
});

it('has transfers', function () {
    $user = User::factory()
        ->has(Transfer::factory()->count(2))
        ->create();

    expect($user->transfers)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Transfer::class);
});
