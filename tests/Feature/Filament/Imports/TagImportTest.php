<?php

use App\Filament\Resources\TagResource\Pages\ListTags;
use App\Models\Tag;
use App\Models\User;
use Filament\Actions\ImportAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('imports tags', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $csv = UploadedFile::fake()->createWithContent(
        'accounts.csv',
        Str::of('name')->newLine()
            ->append('Tag 1')->toString()
    );

    livewire(ListTags::class)
        ->callAction(ImportAction::class, [
            'file' => $csv,
        ]);

    $this->assertDatabaseHas(Tag::class, [
        'name' => 'Tag 1',
    ]);
});
