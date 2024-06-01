<?php

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use App\Models\Balance;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Livewire\livewire;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Carbon::setTestNow(today());

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('creates balance initial entry when created', function () {
    $newData = Account::factory()->make();

    livewire(AccountResource\Pages\CreateAccount::class)
        ->fillForm([
            'name' => $newData->name,
            'account_type' => $newData->account_type,
            'current_balance' => $newData->current_balance,
        ])
        ->call('create')
        ->assertOk();

    $account = Account::latest()->first();

    $this->assertDatabaseHas(Balance::class, [
        'account_id' => $account->id,
        'balance' => $newData->current_balance,
        'is_initial_record' => true,
        'recorded_until' => today()->subDay(),
    ]);
});
