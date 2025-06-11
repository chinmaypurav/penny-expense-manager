<?php

use App\Enums\PanelId;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

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

it('can render any user edit page', function () {
    $this->get(UserResource::getUrl('edit', ['record' => User::factory()->create()]))
        ->assertForbidden();
});
