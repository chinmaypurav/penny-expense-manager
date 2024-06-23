<?php

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render categories list page', function () {
    Category::factory()->create();

    $this->get(CategoryResource::getUrl('index'))->assertSuccessful();
});

it('can create category', function () {

    $newData = Category::factory()->make();

    livewire(CategoryResource\Pages\CreateCategory::class)
        ->fillForm([
            'name' => $newData->name,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Category::class, [
        'name' => $newData->name,
    ]);
});

it('can render category edit page', function () {
    $this->get(CategoryResource::getUrl('edit', [
        'record' => Category::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve category data', function () {
    $category = Category::factory()->create();

    livewire(CategoryResource\Pages\EditCategory::class, [
        'record' => $category->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $category->name,
        ]);
});

it('can update category', function () {

    $category = Category::factory()->create();

    $newData = Category::factory()->make();

    livewire(CategoryResource\Pages\EditCategory::class, [
        'record' => $category->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newData->name,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Category::class, [
        'name' => $newData->name,
    ]);
});

it('can delete category', function () {

    $category = Category::factory()->create();

    livewire(CategoryResource\Pages\EditCategory::class, [
        'record' => $category->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertSoftDeleted($category);
});
