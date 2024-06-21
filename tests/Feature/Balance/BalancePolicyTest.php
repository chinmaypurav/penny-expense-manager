<?php

use App\Filament\Resources\BalanceResource;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    $account1 = Account::factory()->for($this->user)->create([
        'name' => 'User 1 Account',
    ]);

    $account2 = Account::factory()->for(User::factory())->create([
        'name' => 'User 2 Account',
    ]);

    $this->actingAs($this->user);

});

it('can render balances list page with only current user balances', function () {

    $this->get(BalanceResource::getUrl('index'))
        ->assertSuccessful()
        ->assertSee('User 1 Account')
        ->assertDontSee('User 2 Account');
});
