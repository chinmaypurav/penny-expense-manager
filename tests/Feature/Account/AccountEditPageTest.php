<?php

use App\Enums\PanelId;
use App\Filament\Resources\AccountResource\Pages\EditAccount;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->account = Account::factory()->for($this->user)->create([
        'name' => 'User 1 Account',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('can display initial balance form field', function () {
    livewire(EditAccount::class, ['record' => $this->account->getRouteKey()])
        ->assertFormFieldIsVisible('initialBalance.balance');
});
