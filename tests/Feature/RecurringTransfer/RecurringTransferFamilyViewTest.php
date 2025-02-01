<?php

use App\Enums\PanelId;
use App\Filament\Resources\RecurringTransferResource;
use App\Filament\Resources\RecurringTransferResource\Pages\ListRecurringTransfers;
use App\Models\RecurringTransfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->recurringTransfer = RecurringTransfer::factory()->for($this->user)->create([
        'description' => 'User 1 RecurringTransfer',
    ]);

    PanelId::FAMILY->setCurrentPanel();
});

it('cannot display create action', function () {
    livewire(ListRecurringTransfers::class)
        ->assertActionHidden('create');
});

it('cannot display edit action', function () {
    livewire(ListRecurringTransfers::class)
        ->assertTableActionHidden('edit', $this->recurringTransfer->id);
});

it('cannot display delete action', function () {
    livewire(ListRecurringTransfers::class)
        ->assertTableActionHidden('delete', $this->recurringTransfer->id);
});

it('cannot display bulk delete action', function () {
    livewire(ListRecurringTransfers::class)
        ->set('selectedTableRecords', [$this->recurringTransfer])
        ->assertTableBulkActionHidden('delete');
});

it('cannot render create recurringTransfer page', function () {
    $this->get(RecurringTransferResource::getUrl('create'))
        ->assertForbidden();
});

it('cannot perform recurringTransfer update action', function () {
    livewire(RecurringTransferResource\Pages\EditRecurringTransfer::class, [
        'record' => $this->recurringTransfer->getRouteKey(),
    ])
        ->assertForbidden();
});

it('cannot perform delete recurringTransfer action', function () {
    livewire(RecurringTransferResource\Pages\EditRecurringTransfer::class, [
        'record' => $this->recurringTransfer->getRouteKey(),
    ])
        ->assertForbidden();
});
