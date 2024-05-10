<?php

use App\Models\Account;
use App\Models\Expense;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $expense = Expense::factory()->has(User::factory())->create();

    expect($expense->user)->toBeInstanceOf(User::class);
});

it('belongs to a account', function () {
    $expense = Expense::factory()->has(Account::factory())->create();

    expect($expense->account)->toBeInstanceOf(Account::class);
});

it('belongs to a person', function () {
    $expense = Expense::factory()->has(Person::factory())->create();

    expect($expense->person)->toBeInstanceOf(Person::class);
});
