<?php

use App\Enums\Role;
use App\Filament\Resources\LabelResource;
use App\Models\Label;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

dataset('roles', [
    'spectator' => Role::SPECTATOR,
    'member' => Role::MEMBER,
]);

it('non admin cannot render label list page', function (Role $role) {
    $user = User::factory()->create(['role' => $role]);
    $this->actingAs($user);

    $this->get(LabelResource::getUrl('index'))->assertForbidden();
})->with('roles');

it('non admin cannot create label', function (Role $role) {
    $user = User::factory()->create(['role' => $role]);
    $this->actingAs($user);

    $this->get(LabelResource::getUrl('create'))->assertForbidden();
})->with('roles');

it('non admin cannot render label edit page', function (Role $role) {
    $user = User::factory()->create(['role' => $role]);
    $this->actingAs($user);

    $this->get(LabelResource::getUrl('edit', [
        'record' => Label::factory()->create(),
    ]))->assertForbidden();
})->with('roles');
