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

    $this->transfer1 = Transfer::factory()->for($this->user)->today()->create([
        'description' => 'User 1 Transfer',
    ]);

    $this->transfer2 = Transfer::factory()->for(User::factory())->today()->create([
        'description' => 'User 2 Transfer',
    ]);

    PanelId::FAMILY->setCurrentPanel();
});

it('can display user filter', function () {
    livewire(ListTransfers::class)
        ->assertTableFilterVisible('user');
});

it('can display user columns', function () {
    livewire(ListTransfers::class)
        ->assertTableColumnVisible('user.name');
});

it('display user columns', function () {
    livewire(ListTransfers::class)
        ->assertSee([$this->transfer1->description])
        ->assertSee([$this->transfer2->description]);
});
