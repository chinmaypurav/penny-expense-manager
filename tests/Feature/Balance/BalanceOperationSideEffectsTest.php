<?php

use App\Models\Account;
use App\Models\Balance;
use App\Models\User;
use App\Services\AccountTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

use function Pest\Laravel\mock;

uses(RefreshDatabase::class);

test('it sends transactions over email', function () {
    mock(AccountTransactionService::class, function (MockInterface $mock) {
        $mock->shouldReceive('sendTransactionsOverEmail')->once();
    });

    $user = User::factory()->create();
    $account = Account::factory()->for($user)->createQuietly();
    Balance::factory()->for($account)->create();
});
