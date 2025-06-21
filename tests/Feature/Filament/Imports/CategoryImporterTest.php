<?php

use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Models\Category;
use App\Models\User;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Models\FailedImportRow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('imports categories', function () {
    $csv = UploadedFile::fake()->createWithContent(
        'categories.csv',
        Str::of('name')->newLine()
            ->append('Category 1')->toString()
    );

    livewire(ListCategories::class)
        ->callAction(ImportAction::class, [
            'file' => $csv,
        ]);

    $this->assertDatabaseHas(Category::class, [
        'name' => 'Category 1',
    ]);
});

it('records failed import of categories', function () {
    $csv = UploadedFile::fake()->createWithContent(
        'categories.csv',
        Str::of('name')->newLine()
            ->append(Str::random(256))->toString()
    );

    livewire(ListCategories::class)
        ->callAction(ImportAction::class, [
            'file' => $csv,
        ]);

    $this->assertDatabaseCount(FailedImportRow::class, 1);

    $this->assertDatabaseEmpty(Category::class);
});
