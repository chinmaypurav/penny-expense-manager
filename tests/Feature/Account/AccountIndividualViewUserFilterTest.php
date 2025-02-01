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

    $this->account2 = Account::factory(User::factory())->create([
        'name' => 'User 2 Account',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('cannot display user filter', function () {
    livewire(ListAccounts::class)
        ->assertTableFilterHidden('user');
});

it('cannot display user columns', function () {
    livewire(ListAccounts::class)
        ->assertTableColumnHidden('user.name');
});

it('can only list auth user accounts', function () {
    livewire(ListAccounts::class)
        ->assertSee([$this->account1->name])
        ->assertDontSee([$this->account2->name]);
});
