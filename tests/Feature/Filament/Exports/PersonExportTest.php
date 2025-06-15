<?php

use App\Filament\Resources\PersonResource\Pages\ListPeople;
use App\Models\Person;
use App\Models\User;
use Filament\Actions\ExportAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('exports people', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Person::factory()->create();

    Storage::fake('local');

    livewire(ListPeople::class)
        ->callAction(ExportAction::class);

    Storage::disk('local')
        ->assertCount('filament_exports/', 2, true);
});
