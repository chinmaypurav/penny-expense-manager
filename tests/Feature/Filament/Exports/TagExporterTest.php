<?php

use App\Filament\Exports\TagExporter;
use App\Filament\Resources\TagResource\Pages\ListTags;
use App\Models\Tag;
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

it('exports tags', function () {
    Tag::factory()->create();

    Storage::fake('local');

    livewire(ListTags::class)
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
    $notificationBody = TagExporter::getCompletedNotificationBody($mockExport);

    // Assertions
    expect($notificationBody)->toContain('5 rows exported.') // To check successful rows text
        ->and('3 rows failed to export.'); // To verify the failed rows text
});
