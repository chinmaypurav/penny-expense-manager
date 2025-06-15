<?php

use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Models\Category;
use App\Models\User;
use Filament\Actions\ExportAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('exports categories', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Category::factory()->create();

    Storage::fake('local');

    livewire(ListCategories::class)
        ->callAction(ExportAction::class);

    Storage::disk('local')
        ->assertCount('filament_exports/', 2, true);
});
