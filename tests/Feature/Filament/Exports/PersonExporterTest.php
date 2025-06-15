<?php

use App\Filament\Exports\PersonExporter;
use App\Filament\Resources\PersonResource\Pages\ListPeople;
use App\Models\Person;
use App\Models\User;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('exports people', function () {
    Person::factory()->create();

    Storage::fake('local');

    livewire(ListPeople::class)
        ->callAction(ExportAction::class);

    Storage::disk('local')
        ->assertCount('filament_exports/', 2, true);
});

it('includes failed rows in the notification body when rows fail to export', function () {
    // Mock the Export model
    $mockExport = Mockery::mock(Export::class);

    // Set up expectations for the mock
    $mockExport->shouldReceive('getAttribute')->with('successful_rows')
        ->andReturn(5); // Example: 5 successful rows

    $mockExport->shouldReceive('getFailedRowsCount')
        ->andReturn(3); // Example: 3 failed rows

    // Call the method under test
    $notificationBody = PersonExporter::getCompletedNotificationBody($mockExport);

    // Assertions
    expect($notificationBody)->toContain('5 rows exported.') // To check successful rows text
        ->and('3 rows failed to export.'); // To verify the failed rows text
});
