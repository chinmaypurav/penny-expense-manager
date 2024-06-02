<?php

use App\Filament\Resources\LabelResource;
use App\Models\Label;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render label list page', function () {
    $this->get(LabelResource::getUrl('index'))->assertSuccessful();
});

it('can create label', function () {
    $newData = Label::factory()->make();

    livewire(LabelResource\Pages\CreateLabel::class)
        ->fillForm([
            'name' => $newData->name,
            'color' => $newData->color,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Label::class, [
        'name' => $newData->name,
        'color' => $newData->color,
    ]);
});

it('can render label edit page', function () {
    $this->get(LabelResource::getUrl('edit', [
        'record' => Label::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve label data', function () {
    $label = Label::factory()->create();

    livewire(LabelResource\Pages\EditLabel::class, [
        'record' => $label->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $label->name,
            'color' => $label->color,
        ]);
});

it('can update label', function () {
    $label = Label::factory()->create();
    $newData = Label::factory()->make();

    livewire(LabelResource\Pages\EditLabel::class, [
        'record' => $label->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newData->name,
            'color' => $newData->color,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Label::class, [
        'name' => $newData->name,
        'color' => $newData->color,
    ]);
});

it('can soft delete label', function () {

    $label = Label::factory()->create();

    livewire(LabelResource\Pages\EditLabel::class, [
        'record' => $label->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertSoftDeleted($label);
});

it('can restore deleted label from edit page', function () {

    $label = Label::factory()->deleted()->create();

    livewire(LabelResource\Pages\EditLabel::class, [
        'record' => $label->getRouteKey(),
    ])
        ->callAction(RestoreAction::class);

    $this->assertModelExists($label);
});
