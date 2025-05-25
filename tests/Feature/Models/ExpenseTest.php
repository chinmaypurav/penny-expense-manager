<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Person;
use App\Models\Tag;
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

it('belongs to a category', function () {
    $expense = Expense::factory()->has(Category::factory())->create();

    expect($expense->category)->toBeInstanceOf(Category::class);
});

it('has many tags', function () {
    $expense = Expense::factory()->hasAttached(
        Tag::factory(2)
    )->create();

    expect($expense->tags)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Tag::class);
});

it('has today expenses', function () {
    $expense = Expense::factory()->today()->create();
    Expense::factory()->yesterday()->create();

    $expenses = Expense::today()->get();

    expect($expenses)->toHaveCount(1)
        ->and($expenses->value('id'))->toBe($expense->id);
});

it('has transacted on expenses', function () {
    Expense::factory()->today()->create();
    $expense = Expense::factory()->yesterday()->create();

    $expenses = Expense::transactionOn(today()->subDay())->get();

    expect($expenses)->toHaveCount(1)
        ->and($expenses->value('id'))->toBe($expense->id);
});
