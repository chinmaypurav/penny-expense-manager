<?php

use App\Filament\Resources\TagResource\Pages\ListTags;
use App\Models\Tag;
use App\Models\User;
use Filament\Actions\ExportAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('exports tags', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Tag::factory()->create();

    Storage::fake('local');

    livewire(ListTags::class)
        ->callAction(ExportAction::class);

    Storage::disk('local')
        ->assertCount('filament_exports/', 2, true);
});
