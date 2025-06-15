<?php

use App\Enums\PanelId;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    PanelId::FAMILY->setCurrentPanel();
});

it('can render users list page', function () {
    $this->get(UserResource::getUrl('index'))
        ->assertSuccessful();
});

it('can create user', function () {
    $newData = User::factory()->make();

    livewire(CreateUser::class)
        ->fillForm([
            'name' => $newData->name,
            'email' => $newData->email,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(User::class, [
        'name' => $newData->name,
        'email' => $newData->email,
    ]);
});

it('can render user edit page', function () {
    $this->get(UserResource::getUrl('edit', [
        'record' => User::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve user data', function () {
    $user = User::factory()->create();

    livewire(EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $user->name,
            'email' => $user->email,
        ]);
});

it('can update user', function () {
    $user = User::factory()->create();
    $newData = User::factory()->make();

    livewire(EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newData->name,
            'email' => $newData->email,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(User::class, [
        'id' => $user->id,
        'name' => $newData->name,
        'email' => $newData->email,
    ]);
});

it('can soft delete user', function () {
    $user = User::factory()->create();

    livewire(EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    assertSoftDeleted($user);
});
