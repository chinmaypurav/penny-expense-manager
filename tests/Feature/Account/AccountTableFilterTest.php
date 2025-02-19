<?php

use App\Enums\AccountType;
use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can filter selected account types only', function () {
    $savingAccounts = Account::factory(3)
        ->for($this->user)
        ->create(['account_type' => AccountType::SAVINGS]);

    $currentAccounts = Account::factory(2)
        ->for($this->user)
        ->create(['account_type' => AccountType::CURRENT]);

    livewire(ListAccounts::class)
        ->assertCountTableRecords(5)
        ->filterTable('account_type', [AccountType::SAVINGS->value])
        ->assertCountTableRecords(3)
        ->assertCanSeeTableRecords($savingAccounts)
        ->assertCanNotSeeTableRecords($currentAccounts);
});
