<?php

namespace Tests\Feature\Services;

use App\Models\Account;
use App\Models\User;
use App\Services\TableFilterService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('is ordered in asc order', function () {
    $user = User::factory()->create();
    Account::factory()->for($user)->create(['name' => 'Charlie']);
    Account::factory()->for($user)->create(['name' => 'Alfa']);
    Account::factory()->for($user)->create(['name' => 'Bravo']);

    $accounts = TableFilterService::getAccountsFilter($user->id);

    expect($accounts)->sequence(
        fn ($account) => $account->toBe('Alfa'),
        fn ($account) => $account->toBe('Bravo'),
        fn ($account) => $account->toBe('Charlie'),
    );

});
