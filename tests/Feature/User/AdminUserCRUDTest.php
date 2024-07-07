<?php

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
    $this->actingAs($this->user);
});

it('admin can render user list page', function () {
    $this->get(UserResource::getUrl('index'))->assertSuccessful();
});

it('admin can create user', function () {
    $newData = User::factory()->make();

    livewire(UserResource\Pages\CreateUser::class)
        ->fillForm([
            'name' => $newData->name,
            'email' => $newData->email,
            'role' => $newData->role,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(User::class, [
        'name' => $newData->name,
        'email' => $newData->email,
        'role' => $newData->role,
    ]);
});

it('admin can render user edit page', function () {
    $this->get(UserResource::getUrl('edit', [
        'record' => User::factory()->create(),
    ]))->assertSuccessful();
});

it('admin can retrieve user data', function () {
    $user = User::factory()->create();

    livewire(UserResource\Pages\EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
        ]);
});

it('admin can update user', function () {
    $user = User::factory()->create();
    $newData = User::factory()->make();

    livewire(UserResource\Pages\EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newData->name,
            'email' => $newData->email,
            'role' => $newData->role,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(User::class, [
        'name' => $newData->name,
        'email' => $newData->email,
        'role' => $newData->role,
    ]);
});

it('admin can soft delete user', function () {

    $user = User::factory()->create();

    livewire(UserResource\Pages\EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertSoftDeleted($user);
});

it('admin can restore deleted user from edit page', function () {

    $user = User::factory()->deleted()->create();

    livewire(UserResource\Pages\EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->callAction(RestoreAction::class);

    $this->assertModelExists($user);
});
