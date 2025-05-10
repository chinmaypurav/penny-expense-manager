<?php

use App\Filament\Resources\BalanceResource;
use App\Models\Account;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render balance list page', function () {
    $this->get(BalanceResource::getUrl('index'))->assertSuccessful();
});

it('can display transactions action only for monthly and yearly record types', function () {
    $account = Account::factory()->for($this->user)->create();
    $initial = Balance::factory()->for($account)->initialRecord()->create();
    $monthly = Balance::factory()->for($account)->monthly()->create();
    $yearly = Balance::factory()->for($account)->yearly()->create();

    livewire(BalanceResource\Pages\ListBalances::class)
        ->assertTableActionVisible('transactions', $monthly)
        ->assertTableActionVisible('transactions', $yearly)
        ->assertTableActionHidden('transactions', $initial);
});
