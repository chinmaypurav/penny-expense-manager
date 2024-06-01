<?php

use App\Filament\Resources\BalanceResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render balance list page', function () {
    $this->get(BalanceResource::getUrl('index'))->assertSuccessful();
});
