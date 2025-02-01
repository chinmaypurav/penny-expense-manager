<?php

use App\Enums\PanelId;
use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->account1 = Account::factory()->for($this->user)->create([
        'name' => 'User 1 Account',
    ]);

    $this->account2 = Account::factory()->create([
        'name' => 'User 2 Account',
    ]);

    PanelId::FAMILY->setCurrentPanel();
});

it('can display user filter', function () {
    livewire(ListAccounts::class)
        ->assertTableFilterVisible('user');
});

it('can display user columns', function () {
    livewire(ListAccounts::class)
        ->assertTableColumnVisible('user.name');
});

it('display user columns', function () {
    livewire(ListAccounts::class)
        ->assertSee([$this->account1->name])
        ->assertSee([$this->account2->name]);
});
