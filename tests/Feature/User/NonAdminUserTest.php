<?php

use App\Enums\Role;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('non admin cannot render user list page', function (Role $role) {
    $user = User::factory()->create(['role' => $role]);
    $this->actingAs($user);

    $this->get(UserResource::getUrl('index'))->assertForbidden();
})->with([
    'spectator' => [Role::SPECTATOR],
    'member' => [Role::MEMBER],
]);

it('non admin cannot create user', function (Role $role) {
    $user = User::factory()->create(['role' => $role]);
    $this->actingAs($user);

    $this->get(UserResource::getUrl('create'))->assertForbidden();
})->with([
    'spectator' => [Role::SPECTATOR],
    'member' => [Role::MEMBER],
]);

it('non admin cannot render user edit page', function (Role $role) {
    $user = User::factory()->create(['role' => $role]);
    $this->actingAs($user);

    $this->get(UserResource::getUrl('edit', [
        'record' => User::factory()->create(),
    ]))->assertForbidden();
})->with([
    'spectator' => [Role::SPECTATOR],
    'member' => [Role::MEMBER],
]);
