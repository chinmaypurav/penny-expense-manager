<?php

use App\Filament\Resources\PersonResource\Pages\ListPeople;
use App\Models\Person;
use App\Models\User;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Models\FailedImportRow;
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

it('records failed import of people', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $csv = UploadedFile::fake()->createWithContent(
        'people.csv',
        Str::of('name')->newLine()
            ->append(Str::random(256))->toString()
    );

    livewire(ListPeople::class)
        ->callAction(ImportAction::class, [
            'file' => $csv,
        ]);

    $this->assertDatabaseCount(FailedImportRow::class, 1);

    $this->assertDatabaseEmpty(Person::class);
});
