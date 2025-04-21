<?php

use App\Enums\PanelId;
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

    PanelId::APP->setCurrentPanel();
});

it('can display create action', function () {
    livewire(ListTransfers::class)
        ->assertActionVisible('create');
});

it('can display replicate action', function () {
    livewire(ListTransfers::class)
        ->assertTableActionVisible('replicate', $this->transfer->id);
});

it('can display edit action', function () {
    livewire(ListTransfers::class)
        ->assertTableActionVisible('edit', $this->transfer->id);
});

it('can display delete action', function () {
    livewire(ListTransfers::class)
        ->assertTableActionVisible('delete', $this->transfer->id);
});

it('can display import action', function () {
    livewire(ListTransfers::class)
        ->assertActionVisible('import');
});

it('can display bulk delete action', function () {
    livewire(ListTransfers::class)
        ->set('selectedTableRecords', [$this->transfer])
        ->assertTableBulkActionVisible('delete');
});
