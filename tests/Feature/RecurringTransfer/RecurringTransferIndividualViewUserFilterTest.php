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

    $this->recurringTransfer2 = RecurringTransfer::factory(User::factory())->create([
        'description' => 'User 2 RecurringTransfer',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('cannot display user filter', function () {
    livewire(ListRecurringTransfers::class)
        ->assertTableFilterHidden('user');
});

it('cannot display user columns', function () {
    livewire(ListRecurringTransfers::class)
        ->assertTableColumnHidden('user.name');
});

it('can only list auth user recurringTransfers', function () {
    livewire(ListRecurringTransfers::class)
        ->assertSee([$this->recurringTransfer1->description])
        ->assertDontSee([$this->recurringTransfer2->description]);
});
