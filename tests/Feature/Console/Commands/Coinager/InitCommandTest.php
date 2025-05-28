<?php

use App\Console\Commands\Coinager\InitCommand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns early when user already exists', function () {
    User::factory()->create();
    $this->artisan(InitCommand::class)
        ->expectsOutput('Project already initialized.')
        ->assertSuccessful();
});

it('initializes project when no user exists', function () {
    $this->assertDatabaseEmpty(User::class);

    $this->artisan(InitCommand::class)
        ->expectsQuestion('Enter email', $email = 'test@example.com')
        ->expectsQuestion('Enter password', $password = 'password')
        ->expectsQuestion('Enter name', $name = 'Test User')
        ->assertSuccessful();

    $this->assertDatabaseHas(User::class, [
        'email' => $email,
        'name' => $name,
    ]);
});
