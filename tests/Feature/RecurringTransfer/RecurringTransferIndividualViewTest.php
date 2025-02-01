<?php

use App\Enums\PanelId;
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

    PanelId::APP->setCurrentPanel();
});

it('can display create action', function () {
    livewire(ListRecurringTransfers::class)
        ->assertActionVisible('create');
});

it('can display edit action', function () {
    livewire(ListRecurringTransfers::class)
        ->assertTableActionVisible('edit', $this->recurringTransfer->id);
});

it('can display delete action', function () {
    livewire(ListRecurringTransfers::class)
        ->assertTableActionVisible('delete', $this->recurringTransfer->id);
});

it('can display bulk delete action', function () {
    livewire(ListRecurringTransfers::class)
        ->set('selectedTableRecords', [$this->recurringTransfer])
        ->assertTableBulkActionVisible('delete');
});
