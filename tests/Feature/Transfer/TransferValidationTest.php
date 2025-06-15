<?php

use App\Filament\Resources\TransferResource\Pages\CreateTransfer;
use App\Filament\Resources\TransferResource\Pages\EditTransfer;
use App\Models\Account;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('cannot have same account as debtor and creditor on save', function () {
    $newData = Transfer::factory()->for($this->user)->make();
    $account = Account::factory()->create();

    livewire(CreateTransfer::class)
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

it('cannot change accounts on update', function () {
    $transfer = Transfer::factory()->for($this->user)->create();

    livewire(EditTransfer::class, [
        'record' => $transfer->getRouteKey(),
    ])
        ->assertFormFieldIsDisabled('creditor_id')
        ->assertFormFieldIsDisabled('debtor_id');
});

it('cannot add future date as transacted_at', function () {
    $newData = Transfer::factory()->for($this->user)->tomorrow()->make();

    livewire(CreateTransfer::class)
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

    livewire(EditTransfer::class, [
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
