<?php

use App\Enums\PanelId;
use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user1 = User::factory()->create([
        'name' => 'User 1',
    ]);
    $this->actingAs($this->user1);

    $this->user2 = User::factory()->create([
        'name' => 'User 2',
    ]);

    PanelId::APP->setCurrentPanel();
});

it('cannot render user list', function () {
    $this->get(UserResource::getUrl('index'))
        ->assertForbidden();
});

it('cannot render user create', function () {
    $this->get(UserResource::getUrl('create'))
        ->assertForbidden();
});

it('cannot render any user edit page', function () {
    $this->get(UserResource::getUrl('edit', ['record' => User::factory()->create()]))
        ->assertForbidden();
});

it('cannot delete any user', function () {
    $this->actingAs($this->user1);

    expect(app(UserPolicy::class)->delete($this->user1, $this->user2))->toBe(false);
});
