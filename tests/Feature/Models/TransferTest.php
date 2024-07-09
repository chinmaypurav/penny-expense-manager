<?php

use App\Models\Account;
use App\Models\Tag;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $transfer = Transfer::factory()->has(User::factory())->create();

    expect($transfer->user)->toBeInstanceOf(User::class);
});

it('belongs to a creditor account', function () {
    $transfer = Transfer::factory()->has(Account::factory(), 'creditor')->create();

    expect($transfer->creditor)->toBeInstanceOf(Account::class);
});

it('belongs to a debtor account', function () {
    $transfer = Transfer::factory()->has(Account::factory(), 'debtor')->create();

    expect($transfer->debtor)->toBeInstanceOf(Account::class);
});

it('has many tags', function () {
    $transfer = Transfer::factory()->hasAttached(
        Tag::factory(2)
    )->create();

    expect($transfer->tags)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Tag::class);
});
