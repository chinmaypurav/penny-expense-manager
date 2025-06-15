<?php

use App\Filament\Resources\RecurringTransferResource;
use App\Filament\Resources\RecurringTransferResource\Pages\CreateRecurringTransfer;
use App\Filament\Resources\RecurringTransferResource\Pages\EditRecurringTransfer;
use App\Models\Account;
use App\Models\RecurringTransfer;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render recurring recurring transfers list page', function () {
    RecurringTransfer::factory()->for($this->user)->create();

    $this->get(RecurringTransferResource::getUrl('index'))->assertSuccessful();
});

it('can create recurring transfer', function () {

    $newData = RecurringTransfer::factory()->make();
    $creditor = Account::factory()->create();
    $debtor = Account::factory()->create();

    livewire(CreateRecurringTransfer::class)
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
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(RecurringTransfer::class, [
        'description' => $newData->description,
        'creditor_id' => $creditor->id,
        'debtor_id' => $debtor->id,
        'amount' => $newData->amount,
        'next_transaction_at' => $newData->next_transaction_at->toDateString(),
        'remaining_recurrences' => $newData->remaining_recurrences,
        'frequency' => $newData->frequency,
    ]);
});

it('can render recurring transfer edit page', function () {
    $this->get(RecurringTransferResource::getUrl('edit', [
        'record' => RecurringTransfer::factory()->for($this->user)->create(),
    ]))->assertSuccessful();
});

it('can retrieve recurring transfer data', function () {
    $recurringTransfer = RecurringTransfer::factory()->for($this->user)->create();

    livewire(EditRecurringTransfer::class, [
        'record' => $recurringTransfer->getRouteKey(),
    ])
        ->assertFormSet([
            'description' => $recurringTransfer->description,
            'creditor_id' => $recurringTransfer->creditor_id,
            'debtor_id' => $recurringTransfer->debtor_id,
            'amount' => $recurringTransfer->amount,
            'next_transaction_at' => $recurringTransfer->next_transaction_at->toDateString(),
            'remaining_recurrences' => $recurringTransfer->remaining_recurrences,
            'frequency' => $recurringTransfer->frequency,
        ]);
});

it('can update recurring transfer', function () {

    $recurringTransfer = RecurringTransfer::factory()->for($this->user)->create();

    $creditor = Account::factory()->create();
    $debtor = Account::factory()->create();
    $newData = RecurringTransfer::factory()->make();

    livewire(EditRecurringTransfer::class, [
        'record' => $recurringTransfer->getRouteKey(),
    ])
        ->fillForm([
            'description' => $newData->description,
            'creditor_id' => $creditor->id,
            'debtor_id' => $debtor->id,
            'amount' => $newData->amount,
            'next_transaction_at' => $newData->next_transaction_at,
            'remaining_recurrences' => $newData->remaining_recurrences,
            'frequency' => $newData->frequency,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(RecurringTransfer::class, [
        'description' => $newData->description,
        'creditor_id' => $creditor->id,
        'debtor_id' => $debtor->id,
        'amount' => $newData->amount,
        'next_transaction_at' => $newData->next_transaction_at->toDateString(),
        'remaining_recurrences' => $newData->remaining_recurrences,
        'frequency' => $newData->frequency,
    ]);
});

it('can delete recurring transfer', function () {

    $recurringTransfer = RecurringTransfer::factory()->for($this->user)->create();

    livewire(EditRecurringTransfer::class, [
        'record' => $recurringTransfer->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($recurringTransfer);
});
