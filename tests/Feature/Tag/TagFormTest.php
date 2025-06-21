<?php

use App\Filament\Resources\TagResource\Pages\CreateTag;
use App\Models\User;
use Filament\Forms\Components\ColorPicker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('has a default random color populated', function () {
    livewire(CreateTag::class)
        ->assertFormFieldExists(
            'color',
            function (ColorPicker $field) {
                expect($field->getState())->toMatch('/^#[A-F0-9]{6}$/');

                return true;
            }
        )
        ->assertHasNoFormErrors();
});
