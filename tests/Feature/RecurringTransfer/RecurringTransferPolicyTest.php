<?php

use App\Filament\Resources\RecurringTransferResource;
use App\Filament\Resources\RecurringTransferResource\Pages\EditRecurringTransfer;
use App\Models\RecurringTransfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    RecurringTransfer::factory()->for($this->user)->create([
        'description' => 'User 1 Recurring Transfer',
    ]);

    RecurringTransfer::factory()->for(User::factory())->create([
        'description' => 'User 2 Recurring Transfer',
    ]);
});

it('can render recurring transfers list page with only current user recurring transfers', function () {

    $this->get(RecurringTransferResource::getUrl('index'))
        ->assertSuccessful()
        ->assertSee('User 1 Recurring Transfer')
        ->assertDontSee('User 2 Recurring Transfer');
});

it('cannot render recurring transfer edit page for another user', function () {
    $this->get(RecurringTransferResource::getUrl('edit', [
        'record' => RecurringTransfer::factory()->create(),
    ]))->assertForbidden();
});

it('cannot retrieve recurring transfer data for another user', function () {
    $recurringTransfer = RecurringTransfer::factory()->create();

    livewire(EditRecurringTransfer::class, [
        'record' => $recurringTransfer->getRouteKey(),
    ])
        ->assertForbidden();
});
