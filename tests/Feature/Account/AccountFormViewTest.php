<?php

use App\Enums\PanelId;
use App\Filament\Resources\AccountResource\Pages\CreateAccount;
use App\Filament\Resources\AccountResource\Pages\EditAccount;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->account = Account::factory()->for($this->user)->create();

    PanelId::APP->setCurrentPanel();
});

it('loads Initial Balance when creating account', function () {
    livewire(CreateAccount::class)
        ->assertSeeText('Initial Balance')
        ->assertDontSeeText('Current Balance');
});

it('loads Current Balance when editing account', function () {
    livewire(EditAccount::class, ['record' => $this->account->getRouteKey()])
        ->assertSeeHtml('Current Balance');
});

it('cannot display initial balance form field on create', function () {
    livewire(CreateAccount::class)
        ->assertFormFieldIsHidden('initialBalance.balance');
});

it('can display initial balance form field', function () {
    livewire(EditAccount::class, ['record' => $this->account->getRouteKey()])
        ->assertFormFieldIsVisible('initialBalance.balance');
});
