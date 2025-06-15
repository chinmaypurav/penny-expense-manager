<?php

use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Models\Account;
use App\Models\User;
use App\Services\AccountTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

use function Pest\Laravel\mock;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can display transactions action ', function () {
    $account = Account::factory()->for($this->user)->create();

    livewire(ListAccounts::class)
        ->assertTableActionVisible('transactions', $account);
});

it('sends transactions over email', function () {
    $account = Account::factory()->for($this->user)->create();

    mock(AccountTransactionService::class, function (MockInterface $mock) {
        $mock->shouldReceive('sendProvisionalTransactions')->once();
    });

    livewire(ListAccounts::class)
        ->callTableAction('transactions', $account);
});
