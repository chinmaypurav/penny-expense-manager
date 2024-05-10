<?php

use App\Models\Account;
use App\Models\Income;
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
