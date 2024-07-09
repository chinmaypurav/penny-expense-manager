<?php

use App\Filament\Resources\TagResource;
use App\Models\Tag;
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

it('can render tag list page', function () {
    $this->get(TagResource::getUrl('index'))->assertSuccessful();
});

it('can create tag', function () {
    $newData = Tag::factory()->make();

    livewire(TagResource\Pages\CreateTag::class)
        ->fillForm([
            'name' => $newData->name,
            'color' => $newData->color,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Tag::class, [
        'name' => $newData->name,
        'color' => $newData->color,
    ]);
});

it('can render tag edit page', function () {
    $this->get(TagResource::getUrl('edit', [
        'record' => Tag::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve tag data', function () {
    $tag = Tag::factory()->create();

    livewire(TagResource\Pages\EditTag::class, [
        'record' => $tag->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $tag->name,
            'color' => $tag->color,
        ]);
});

it('can update tag', function () {
    $tag = Tag::factory()->create();
    $newData = Tag::factory()->make();

    livewire(TagResource\Pages\EditTag::class, [
        'record' => $tag->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newData->name,
            'color' => $newData->color,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Tag::class, [
        'name' => $newData->name,
        'color' => $newData->color,
    ]);
});

it('can soft delete tag', function () {

    $tag = Tag::factory()->create();

    livewire(TagResource\Pages\EditTag::class, [
        'record' => $tag->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertSoftDeleted($tag);
});

it('can restore deleted label from edit page', function () {

    $tag = Tag::factory()->deleted()->create();

    livewire(TagResource\Pages\EditTag::class, [
        'record' => $tag->getRouteKey(),
    ])
        ->callAction(RestoreAction::class);

    $this->assertModelExists($tag);
});
