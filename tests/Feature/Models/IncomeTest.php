<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Income;
use App\Models\Person;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $income = Income::factory()->has(User::factory())->create();

    expect($income->user)->toBeInstanceOf(User::class);
});

it('belongs to a account', function () {
    $income = Income::factory()->has(Account::factory())->create();

    expect($income->account)->toBeInstanceOf(Account::class);
});

it('belongs to a person', function () {
    $income = Income::factory()->has(Person::factory())->create();

    expect($income->person)->toBeInstanceOf(Person::class);
});

it('belongs to a category', function () {
    $income = Income::factory()->has(Category::factory())->create();

    expect($income->category)->toBeInstanceOf(Category::class);
});

it('has many tags', function () {
    $income = Income::factory()->hasAttached(
        Tag::factory(2)
    )->create();

    expect($income->tags)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Tag::class);
});

it('has today incomes', function () {
    $income = Income::factory()->today()->create();
    Income::factory()->yesterday()->create();

    $incomes = Income::today()->get();

    expect($incomes)->toHaveCount(1)
        ->and($incomes->value('id'))->toBe($income->id);
});

it('has transacted on incomes', function () {
    Income::factory()->today()->create();
    $income = Income::factory()->yesterday()->create();

    $incomes = Income::transactionOn(today()->subDay())->get();

    expect($incomes)->toHaveCount(1)
        ->and($incomes->value('id'))->toBe($income->id);
});
