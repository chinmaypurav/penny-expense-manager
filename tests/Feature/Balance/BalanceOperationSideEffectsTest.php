<?php

use App\Enums\RecordType;
use App\Models\Account;
use App\Models\Balance;
use App\Models\User;
use App\Services\AccountTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

use function Pest\Laravel\mock;

uses(RefreshDatabase::class);

it('sends transactions over email for record types other than initial', function (RecordType $recordType) {
    mock(AccountTransactionService::class, function (MockInterface $mock) {
        $mock->shouldReceive('sendTransactionsForBalancePeriod')->once();
    });

    $user = User::factory()->create();
    $account = Account::factory()->for($user)->createQuietly();
    Balance::factory()->for($account)->create([
        'record_type' => $recordType,
    ]);
})->with([
    'monthly' => [RecordType::MONTHLY],
    'yearly' => [RecordType::YEARLY],
]);
