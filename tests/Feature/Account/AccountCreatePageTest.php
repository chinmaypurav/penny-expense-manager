<?php

use App\Enums\PanelId;
use App\Filament\Resources\AccountResource\Pages\CreateAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    PanelId::APP->setCurrentPanel();
});

it('cannot display initial balance form field', function () {
    livewire(CreateAccount::class)
        ->assertFormFieldIsHidden('initialBalance.balance');
});
