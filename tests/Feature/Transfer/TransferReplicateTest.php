<?php

use App\Enums\PanelId;
use App\Filament\Resources\TransferResource\Pages\ListTransfers;
use App\Models\Transfer;
use App\Models\User;
use Filament\Actions\ReplicateAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    PanelId::APP->setCurrentPanel();
});

it('replicates existing transfer with current timestamp', function () {
    $transfer = Transfer::factory()->for($this->user)->today()->create();
    $this->assertDatabaseCount(Transfer::class, 1);

    $this->travel(1)->minute();

    livewire(ListTransfers::class)
        ->callTableAction(ReplicateAction::class, $transfer);

    $expected = $transfer->replicate([
        'id', 'transacted_at',
    ])->withoutRelations()->toArray();
    $expected['transacted_at'] = now();

    $this->assertDatabaseCount(Transfer::class, 2);
    $this->assertDatabaseHas(Transfer::class, $expected);
});
