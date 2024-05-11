<?php

use App\Models\Expense;
use App\Models\Income;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $person = Person::factory()->has(User::factory())->create();

    expect($person->user)->toBeInstanceOf(User::class);
});

it('has incomes', function () {
    $person = Person::factory()
        ->has(Income::factory()->count(2))
        ->create();

    expect($person->incomes)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Income::class);
});

it('has expenses', function () {
    $person = Person::factory()
        ->has(Expense::factory()->count(2))
        ->create();

    expect($person->expenses)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Expense::class);
});
