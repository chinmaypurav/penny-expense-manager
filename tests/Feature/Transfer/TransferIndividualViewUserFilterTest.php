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

    $this->transfer2 = Transfer::factory(User::factory())->today()->create([
        'description' => 'User 2 Transfer',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('cannot display user filter', function () {
    livewire(ListTransfers::class)
        ->assertTableFilterHidden('user');
});

it('cannot display user columns', function () {
    livewire(ListTransfers::class)
        ->assertTableColumnHidden('user.name');
});

it('can only list auth user transfers', function () {
    livewire(ListTransfers::class)
        ->assertSee([$this->transfer1->description])
        ->assertDontSee([$this->transfer2->description]);
});
