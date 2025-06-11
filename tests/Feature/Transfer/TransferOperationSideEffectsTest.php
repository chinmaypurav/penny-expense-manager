<?php

use App\Filament\Resources\TransferResource;
use App\Models\Account;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Livewire\livewire;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->user = User::factory()->createQuietly();
    $this->actingAs($this->user);
});

it('changes account balances on transfer created', function () {
    $ca = Account::factory()->for($this->user)->createQuietly(['current_balance' => 1000]);
    $da = Account::factory()->for($this->user)->createQuietly(['current_balance' => 2000]);

    Transfer::factory()
        ->for($this->user)
        ->for($da, 'debtor')
        ->for($ca, 'creditor')
        ->create([
            'amount' => 3000,
        ]);

    $this->assertDatabaseHas(Account::class, [
        'id' => $ca->id,
        'current_balance' => 4000,
    ]);
    $this->assertDatabaseHas(Account::class, [
        'id' => $da->id,
        'current_balance' => -1000,
    ]);
});

it('changes account balances on transfer updated', function () {
    $ca = Account::factory()->for($this->user)->createQuietly(['current_balance' => 1000]);
    $da = Account::factory()->for($this->user)->createQuietly(['current_balance' => 2000]);

    $transfer = Transfer::factory()
        ->for($this->user)
        ->for($da, 'debtor')
        ->for($ca, 'creditor')
        ->today()
        ->createQuietly([
            'amount' => 3000,
        ])->refresh();

    $transfer->update([
        'amount' => 2000, // amount reduced by 2
    ]);

    $this->assertDatabaseHas(Account::class, [
        'id' => $ca->id,
        'current_balance' => 0,
    ]);
    $this->assertDatabaseHas(Account::class, [
        'id' => $da->id,
        'current_balance' => 3000,
    ]);
});

it('changes account balances on transfer deleted', function () {
    $ca = Account::factory()->for($this->user)->createQuietly(['current_balance' => 1000]);
    $da = Account::factory()->for($this->user)->createQuietly(['current_balance' => 2000]);

    $transfer = Transfer::factory()
        ->for($this->user)
        ->for($da, 'debtor')
        ->for($ca, 'creditor')
        ->today()
        ->createQuietly([
            'amount' => 3000,
        ])->refresh();

    $transfer->delete();

    $this->assertDatabaseHas(Account::class, [
        'id' => $ca->id,
        'current_balance' => -2000,
    ]);
    $this->assertDatabaseHas(Account::class, [
        'id' => $da->id,
        'current_balance' => 5000,
    ]);
});

it('doesnt affect account balances when amount and transacted at clean on transfer update', function () {
    $ca = Account::factory()->for($this->user)->createQuietly(['current_balance' => 1000]);
    $da = Account::factory()->for($this->user)->createQuietly(['current_balance' => 2000]);

    $transfer = Transfer::factory()
        ->for($this->user)
        ->for($da, 'debtor')
        ->for($ca, 'creditor')
        ->today()
        ->createQuietly([
            'amount' => 3000,
        ])->refresh();

    $newData = Transfer::factory()->make();

    livewire(TransferResource\Pages\EditTransfer::class, [
        'record' => $transfer->getRouteKey(),
    ])
        ->fillForm([
            'description' => $newData->description,
        ])
        ->call('save')
        ->assertSuccessful();

    $this->assertDatabaseHas(Account::class, [
        'id' => $ca->id,
        'current_balance' => 1000,
    ]);
    $this->assertDatabaseHas(Account::class, [
        'id' => $da->id,
        'current_balance' => 2000,
    ]);
});
