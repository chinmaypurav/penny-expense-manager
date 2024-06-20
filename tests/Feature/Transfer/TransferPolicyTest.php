<?php

use App\Filament\Resources\TransferResource;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Transfer::factory()->for($this->user)->create([
        'description' => 'User 1 Transfer',
    ]);

    Transfer::factory()->for(User::factory())->create([
        'description' => 'User 2 Transfer',
    ]);
});

it('can render transfers list page with only current user transfers', function () {

    $this->get(TransferResource::getUrl('index'))
        ->assertSuccessful()
        ->assertSee('User 1 Transfer')
        ->assertDontSee('User 2 Transfer');
});

it('cannot render transfer edit page for another user', function () {
    $this->get(TransferResource::getUrl('edit', [
        'record' => Transfer::factory()->create(),
    ]))->assertForbidden();
});

it('cannot retrieve transfer data for another user', function () {
    $transfer = Transfer::factory()->create();

    livewire(TransferResource\Pages\EditTransfer::class, [
        'record' => $transfer->getRouteKey(),
    ])
        ->assertForbidden();
});
