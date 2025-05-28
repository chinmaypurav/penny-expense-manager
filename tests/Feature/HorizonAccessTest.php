<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows access for user id 1', function () {
    $this->actingAs(User::factory()->create(['id' => 1]));
    $this->get(config('horizon.path'))->assertOk();
});

it('denies access for user id not 1', function () {
    $this->actingAs(User::factory()->create(['id' => 2]));
    $this->get(config('horizon.path'))->assertForbidden();
});

it('denies access for guest', function () {
    $this->get(config('horizon.path'))->assertForbidden();
});
