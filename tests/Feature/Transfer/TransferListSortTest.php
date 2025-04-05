<?php

use App\Filament\Resources\TransferResource;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\travelTo;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    travelTo(now()->setDay(20)); // ensure default monthly filter does not exclude results

    Transfer::factory()->for($this->user)->create([
        'description' => 'transfer 15',
        'transacted_at' => now()->setDay(15),
    ]);

    Transfer::factory()->for($this->user)->create([
        'description' => 'transfer 5',
        'transacted_at' => now()->setDay(5),
    ]);

    Transfer::factory()->for($this->user)->create([
        'description' => 'transfer 10',
        'transacted_at' => now()->setDay(10),
    ]);
});

it('sorts transfers list page using transacted_at desc by default', function () {
    Transfer::factory()->for($this->user)->create();

    $this->get(TransferResource::getUrl('index'))->assertSeeTextInOrder([
        'transfer 15', 'transfer 10', 'transfer 5',
    ]);
});
