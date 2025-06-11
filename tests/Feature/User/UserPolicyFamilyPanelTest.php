<?php

use App\Enums\PanelId;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user1 = User::factory()->create([
        'name' => 'User 1',
    ]);

    $this->user2 = User::factory()->create([
        'name' => 'User 2',
    ]);

    PanelId::FAMILY->setCurrentPanel();
});

it('can render user list page when user id 1', function () {
    $this->actingAs($this->user1)
        ->get(UserResource::getUrl('index'))
        ->assertSuccessful();
});

it('cannot render user list page when user id not 1', function () {
    $this->actingAs($this->user2)
        ->get(UserResource::getUrl('index'))
        ->assertForbidden();
});

it('can render user create page when user id 1', function () {
    $this->actingAs($this->user1)
        ->get(UserResource::getUrl('create'))
        ->assertSuccessful();
});

it('cannot render user create page when user id not 1', function () {
    $this->actingAs($this->user2)
        ->get(UserResource::getUrl('create'))
        ->assertForbidden();
});

it('can render user edit page when user id 1', function () {
    $this->actingAs($this->user1)
        ->get(UserResource::getUrl('edit', ['record' => $this->user2->id]))
        ->assertSuccessful();
});

it('cannot render user edit page when user id not 1', function () {
    $this->actingAs($this->user2)
        ->get(UserResource::getUrl('edit', ['record' => $this->user1->id]))
        ->assertForbidden();
});

it('can soft delete another user when user id 1', function () {
    $this->actingAs($this->user1);

    livewire(UserResource\Pages\EditUser::class, [
        'record' => $this->user2->id,
    ])->callAction('delete')
        ->assertSuccessful();
});

it('cannot see table delete action on self user', function () {
    $this->actingAs($this->user1);

    livewire(UserResource\Pages\ListUsers::class)
        ->assertTableActionHidden('delete', $this->user1->id);
});

it('cannot see delete action on self user', function () {
    $this->actingAs($this->user1);

    livewire(UserResource\Pages\EditUser::class, [
        'record' => $this->user1->id,
    ])
        ->assertActionHidden('delete');
});
