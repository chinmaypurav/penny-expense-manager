<?php

use App\Filament\Resources\PersonResource\Pages\ListPeople;
use App\Models\Person;
use App\Models\User;
use Filament\Actions\ImportAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('imports people', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $csv = UploadedFile::fake()->createWithContent(
        'people.csv',
        Str::of('name')->newLine()
            ->append('Person 1')->toString()
    );

    livewire(ListPeople::class)
        ->callAction(ImportAction::class, [
            'file' => $csv,
        ]);

    $this->assertDatabaseHas(Person::class, [
        'name' => 'Person 1',
    ]);
});
