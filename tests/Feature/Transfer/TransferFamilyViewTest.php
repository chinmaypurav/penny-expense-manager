<?php

use App\Enums\PanelId;
use App\Filament\Resources\TransferResource;
use App\Filament\Resources\TransferResource\Pages\ListTransfers;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->transfer = Transfer::factory()->for($this->user)->today()->create([
        'description' => 'User 1 Transfer',
    ]);

    PanelId::FAMILY->setCurrentPanel();
});

it('cannot display create action', function () {
    livewire(ListTransfers::class)
        ->assertActionHidden('create');
});

it('cannot display replicate action', function () {
    livewire(ListTransfers::class)
        ->assertTableActionHidden('replicate', $this->transfer->id);
});

it('cannot display edit action', function () {
    livewire(ListTransfers::class)
        ->assertTableActionHidden('edit', $this->transfer->id);
});

it('cannot display delete action', function () {
    livewire(ListTransfers::class)
        ->assertTableActionHidden('delete', $this->transfer->id);
});

it('cannot display import action', function () {
    livewire(ListTransfers::class)
        ->assertActionHidden('import');
});

it('cannot display bulk delete action', function () {
    livewire(ListTransfers::class)
        ->set('selectedTableRecords', [$this->transfer])
        ->assertTableBulkActionHidden('delete');
});

it('cannot render create transfer page', function () {
    $this->get(TransferResource::getUrl('create'))
        ->assertForbidden();
});

it('cannot perform transfer update action', function () {
    livewire(TransferResource\Pages\EditTransfer::class, [
        'record' => $this->transfer->getRouteKey(),
    ])
        ->assertForbidden();
});

it('cannot perform delete transfer action', function () {
    livewire(TransferResource\Pages\EditTransfer::class, [
        'record' => $this->transfer->getRouteKey(),
    ])
        ->assertForbidden();
});
