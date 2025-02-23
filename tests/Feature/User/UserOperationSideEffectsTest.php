<?php

use App\Enums\PanelId;
use App\Filament\Resources\UserResource;
use App\Mail\SendUserCreatedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Livewire\livewire;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    PanelId::FAMILY->setCurrentPanel();
});

it('sends email to user when created', function () {
    $newData = User::factory()->make();
    Mail::fake();

    livewire(UserResource\Pages\CreateUser::class)
        ->fillForm([
            'name' => $newData->name,
            'email' => $newData->email,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    Mail::assertQueued(function (SendUserCreatedMail $mail) {
        return $mail->afterCommit;
    });
});
