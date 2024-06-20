<?php

use App\Models\Account;
use App\Models\Balance;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to an account', function () {
    $balance = Balance::factory()->has(Account::factory())->create();

    expect($balance->account)->toBeInstanceOf(Account::class);
});
