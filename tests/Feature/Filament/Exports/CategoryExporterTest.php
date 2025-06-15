<?php

use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Models\Category;
use App\Models\User;
use Filament\Actions\ExportAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('exports categories', function () {
    Category::factory()->create();

    Storage::fake('local');

    livewire(ListCategories::class)
        ->callAction(ExportAction::class);

    Storage::disk('local')
        ->assertCount('filament_exports/', 2, true);
});

it('records failed export of categories', function () {
    Category::factory()->create();

    Storage::fake('local', ['read-only' => true]); // to make us throw exceptions

    livewire(ListCategories::class)
        ->callAction(ExportAction::class);

    Storage::disk('local')
        ->assertCount('filament_exports/', 0, true);
});
