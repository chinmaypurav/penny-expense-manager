<?php

use App\Models\Expense;
use App\Models\Income;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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
