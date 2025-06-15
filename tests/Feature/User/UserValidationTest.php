<?php

use App\Enums\PanelId;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    PanelId::FAMILY->setCurrentPanel();
});

it('cannot add duplicate email', function () {
    livewire(CreateUser::class)
        ->fillForm([
            'email' => $this->user->email,
        ])
        ->call('create')
        ->assertHasFormErrors(['email']);
});

it('cannot update duplicate email', function () {
    $user = User::factory()->create();

    livewire(EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->fillForm([
            'email' => $this->user->email,
        ])
        ->call('save')
        ->assertHasFormErrors(['email']);
});

it('can update form with same current email', function () {
    $user = User::factory()->create();

    livewire(EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->fillForm([
            'email' => $user->email,
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});
