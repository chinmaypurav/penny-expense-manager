<?php

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Carbon::setTestNow(now());
});

it('cannot add future date as initial date', function () {
    $newData = Account::factory()->make([
        'initial_date' => today()->addDay(),
    ]);

    livewire(AccountResource\Pages\CreateAccount::class)
        ->fillForm([
            'name' => $newData->name,
            'account_type' => $newData->account_type,
            'current_balance' => $newData->current_balance,
            'initial_date' => $newData->initial_date,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'initial_date',
        ]);
});

it('cannot update future date as initial date', function () {
    $account = Account::factory()->for($this->user)->create();

    livewire(AccountResource\Pages\EditAccount::class, [
        'record' => $account->getRouteKey(),
    ])
        ->fillForm([
            'initial_date' => today()->addDay(),
        ])
        ->call('save')
        ->assertHasFormErrors([
            'initial_date',
        ]);
});
