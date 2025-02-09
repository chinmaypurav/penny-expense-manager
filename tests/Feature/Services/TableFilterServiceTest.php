<?php

namespace Tests\Feature\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use App\Services\TableFilterService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('orders accounts filter in asc order', function () {
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

it('orders categories filter in asc order', function () {
    Category::factory()->create(['name' => 'Charlie']);
    Category::factory()->create(['name' => 'Alfa']);
    Category::factory()->create(['name' => 'Bravo']);

    $categories = TableFilterService::getCategoryFilter();

    expect($categories)->sequence(
        fn ($category) => $category->toBe('Alfa'),
        fn ($account) => $account->toBe('Bravo'),
        fn ($account) => $account->toBe('Charlie'),
    );
});
