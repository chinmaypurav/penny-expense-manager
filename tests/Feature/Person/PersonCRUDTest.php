<?php

use App\Filament\Resources\PersonResource;
use App\Models\Person;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render person list page', function () {
    $this->get(PersonResource::getUrl('index'))->assertSuccessful();
});

it('can create person', function () {
    $newData = Person::factory()->make();

    livewire(PersonResource\Pages\CreatePerson::class)
        ->fillForm([
            'name' => $newData->name,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Person::class, [
        'name' => $newData->name,
    ]);
});

it('can render person edit page', function () {
    $this->get(PersonResource::getUrl('edit', [
        'record' => Person::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve person data', function () {
    $person = Person::factory()->create();

    livewire(PersonResource\Pages\EditPerson::class, [
        'record' => $person->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $person->name,
        ]);
});

it('can update person', function () {
    $person = Person::factory()->create();
    $newData = Person::factory()->make();

    livewire(PersonResource\Pages\EditPerson::class, [
        'record' => $person->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newData->name,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Person::class, [
        'name' => $newData->name,
    ]);
});

it('can delete person', function () {

    $person = Person::factory()->for($this->user)->create();

    livewire(PersonResource\Pages\EditPerson::class, [
        'record' => $person->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($person);
});
