<?php

use App\Filament\Resources\TransferResource;
use App\Filament\Resources\TransferResource\Pages\CreateTransfer;
use App\Filament\Resources\TransferResource\Pages\EditTransfer;
use App\Models\Transfer;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render transfers list page', function () {
    Transfer::factory()->for($this->user)->create();

    $this->get(TransferResource::getUrl('index'))->assertSuccessful();
});

it('can create transfer', function () {

    $newData = Transfer::factory()->for($this->user)->make();

    livewire(CreateTransfer::class)
        ->fillForm([
            'debtor_id' => $newData->debtor->id,
            'creditor_id' => $newData->creditor_id,
            'description' => $newData->description,
            'amount' => $newData->amount,
            'transacted_at' => $newData->transacted_at,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Transfer::class, [
        'debtor_id' => $newData->debtor_id,
        'creditor_id' => $newData->creditor_id,
        'description' => $newData->description,
        'amount' => $newData->amount,
        'transacted_at' => $newData->transacted_at,
    ]);
});

it('can render transfer edit page', function () {
    $this->get(TransferResource::getUrl('edit', [
        'record' => Transfer::factory()->for($this->user)->create(),
    ]))->assertSuccessful();
});

it('can retrieve transfer data', function () {
    $transfer = Transfer::factory()->for($this->user)->create();

    livewire(EditTransfer::class, [
        'record' => $transfer->getRouteKey(),
    ])
        ->assertFormSet([
            'debtor_id' => $transfer->debtor_id,
            'creditor_id' => $transfer->creditor_id,
            'description' => $transfer->description,
            'amount' => $transfer->amount,
            'transacted_at' => $transfer->transacted_at,
        ]);
});

it('can update transfer', function () {
    $transfer = Transfer::factory()->for($this->user)->create();
    $newData = Transfer::factory()->for($this->user)->make([
        'debtor_id' => $transfer->debtor_id,
        'creditor_id' => $transfer->creditor_id,
    ]);

    livewire(EditTransfer::class, [
        'record' => $transfer->getRouteKey(),
    ])
        ->fillForm([
            'description' => $newData->description,
            'amount' => $newData->amount,
            'transacted_at' => $newData->transacted_at,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Transfer::class, [
        'description' => $newData->description,
        'amount' => $newData->amount,
        'transacted_at' => $newData->transacted_at,
    ]);
});

it('can delete transfer', function () {

    $transfer = Transfer::factory()->for($this->user)->create();

    livewire(EditTransfer::class, [
        'record' => $transfer->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($transfer);
});
