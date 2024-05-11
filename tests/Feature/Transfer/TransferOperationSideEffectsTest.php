<?php

use App\Filament\Resources\TransferResource;
use App\Models\Account;
use App\Models\Transfer;
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

it('changes account balances on transfer created', function () {
    $ca = Account::factory()->for($this->user)->create(['balance' => 1000]);
    $da = Account::factory()->for($this->user)->create(['balance' => 2000]);

    $newData = Transfer::factory()
        ->for($this->user)
        ->for($da, 'debtor')
        ->for($ca, 'creditor')
        ->make([
            'amount' => 3000,
        ]);

    livewire(TransferResource\Pages\CreateTransfer::class)
        ->fillForm([
            'debtor_id' => $newData->debtor->id,
            'creditor_id' => $newData->creditor_id,
            'description' => $newData->description,
            'amount' => $newData->amount,
            'transacted_at' => $newData->transacted_at,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Account::class, [
        'id' => $ca->id,
        'balance' => 4000,
    ]);
    $this->assertDatabaseHas(Account::class, [
        'id' => $da->id,
        'balance' => -1000,
    ]);
});
