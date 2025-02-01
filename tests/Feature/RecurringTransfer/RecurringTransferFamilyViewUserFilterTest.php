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

    $this->recurringTransfer1 = RecurringTransfer::factory()->for($this->user)->create([
        'description' => 'User 1 RecurringTransfer',
    ]);

    $this->recurringTransfer2 = RecurringTransfer::factory()->for(User::factory())->create([
        'description' => 'User 2 RecurringTransfer',
    ]);

    PanelId::FAMILY->setCurrentPanel();
});

it('can display user filter', function () {
    livewire(ListRecurringTransfers::class)
        ->assertTableFilterVisible('user');
});

it('can display user columns', function () {
    livewire(ListRecurringTransfers::class)
        ->assertTableColumnVisible('user.name');
});

it('display user columns', function () {
    livewire(ListRecurringTransfers::class)
        ->assertSee([$this->recurringTransfer1->description])
        ->assertSee([$this->recurringTransfer2->description]);
});
