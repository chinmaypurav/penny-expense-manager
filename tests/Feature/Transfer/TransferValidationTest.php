<?php

use App\Filament\Resources\TransferResource;
use App\Models\Account;
use App\Models\Transfer;
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

it('cannot have same account as debtor and creditor on save', function () {
    $newData = Transfer::factory()->for($this->user)->make();
    $account = Account::factory()->create();

    livewire(TransferResource\Pages\CreateTransfer::class)
        ->fillForm([
            'debtor_id' => $account->id,
            'creditor_id' => $account->id,
            'description' => $newData->description,
            'amount' => $newData->amount,
            'transacted_at' => $newData->transacted_at,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'creditor_id',
            'debtor_id',
        ]);
});

it('cannot have same account as debtor and creditor on update', function () {
    $account = Account::factory()->create();

    $transfer = Transfer::factory()->for($this->user)->create();

    livewire(TransferResource\Pages\EditTransfer::class, [
        'record' => $transfer->getRouteKey(),
    ])
        ->fillForm([
            'creditor_id' => $account->id,
            'debtor_id' => $account->id,
        ])
        ->call('save')
        ->assertHasFormErrors([
            'creditor_id',
            'debtor_id',
        ]);
});

it('cannot add future date as transacted_at', function () {
    $newData = Transfer::factory()->for($this->user)->tomorrow()->make();

    livewire(TransferResource\Pages\CreateTransfer::class)
        ->fillForm([
            'debtor_id' => $newData->debtor->id,
            'creditor_id' => $newData->creditor_id,
            'description' => $newData->description,
            'amount' => $newData->amount,
            'transacted_at' => $newData->transacted_at,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'transacted_at',
        ]);
});

it('cannot update future date as transacted_at', function () {
    $transfer = Transfer::factory()->for($this->user)->create();

    livewire(TransferResource\Pages\EditTransfer::class, [
        'record' => $transfer->getRouteKey(),
    ])
        ->fillForm([
            'transacted_at' => now()->addDay(),
        ])
        ->call('save')
        ->assertHasFormErrors([
            'transacted_at',
        ]);
});
