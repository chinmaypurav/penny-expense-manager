<?php

use App\Filament\Resources\AccountResource;
use App\Filament\Resources\AccountResource\Pages\EditAccount;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Account::factory()->for($this->user)->create([
        'name' => 'User 1 Account',
    ]);

    Account::factory()->for(User::factory())->create([
        'name' => 'User 2 Account',
    ]);
});

it('can render accounts list page with only current user accounts', function () {

    $this->get(AccountResource::getUrl('index'))
        ->assertSuccessful()
        ->assertSee('User 1 Account')
        ->assertDontSee('User 2 Account');
});

it('cannot render account edit page for another user', function () {
    $this->get(AccountResource::getUrl('edit', [
        'record' => Account::factory()->create(),
    ]))->assertForbidden();
});

it('cannot retrieve account data for another user', function () {
    $account = Account::factory()->create();

    livewire(EditAccount::class, [
        'record' => $account->getRouteKey(),
    ])
        ->assertForbidden();
});
