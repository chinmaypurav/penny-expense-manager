<?php

use App\Filament\Resources\RecurringTransferResource;
use App\Models\Account;
use App\Models\RecurringTransfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\freezeTime;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    freezeTime();
});

it('cannot add past date as next transaction date', function () {
    $newData = RecurringTransfer::factory()->make([
        'next_transaction_at' => today(),
    ]);
    $creditor = Account::factory()->create();
    $debtor = Account::factory()->create();

    livewire(RecurringTransferResource\Pages\CreateRecurringTransfer::class)
        ->fillForm([
            'description' => $newData->description,
            'creditor_id' => $creditor->id,
            'debtor_id' => $debtor->id,
            'amount' => $newData->amount,
            'next_transaction_at' => $newData->next_transaction_at,
            'remaining_recurrences' => $newData->remaining_recurrences,
            'frequency' => $newData->frequency,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'next_transaction_at',
        ]);
});

it('cannot update past date as next transaction date', function () {

    $recurringTransfer = RecurringTransfer::factory()->for($this->user)->create();

    livewire(RecurringTransferResource\Pages\EditRecurringTransfer::class, [
        'record' => $recurringTransfer->getRouteKey(),
    ])
        ->fillForm([
            'next_transaction_at' => today(),
        ])
        ->call('save')
        ->assertHasFormErrors([
            'next_transaction_at',
        ]);
});

it('cannot have same account as debtor and creditor on save', function () {
    $newData = RecurringTransfer::factory()->make([
        'next_transaction_at' => today(),
    ]);
    $account = Account::factory()->create();

    livewire(RecurringTransferResource\Pages\CreateRecurringTransfer::class)
        ->fillForm([
            'description' => $newData->description,
            'creditor_id' => $account->id,
            'debtor_id' => $account->id,
            'amount' => $newData->amount,
            'next_transaction_at' => $newData->next_transaction_at,
            'remaining_recurrences' => $newData->remaining_recurrences,
            'frequency' => $newData->frequency,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'creditor_id',
            'debtor_id',
        ]);
});

it('cannot have same account as debtor and creditor on update', function () {
    $account = Account::factory()->create();

    $recurringTransfer = RecurringTransfer::factory()->for($this->user)->create();

    livewire(RecurringTransferResource\Pages\EditRecurringTransfer::class, [
        'record' => $recurringTransfer->getRouteKey(),
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
