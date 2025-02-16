<?php

use App\Filament\Resources\RecurringTransferResource;
use App\Models\RecurringTransfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\travelTo;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    travelTo(now()->setDay(20)); // ensure default monthly filter does not exclude results

    RecurringTransfer::factory()->for($this->user)->create([
        'description' => 'recurring transfer 15',
        'next_transaction_at' => now()->setDay(15),
    ]);

    RecurringTransfer::factory()->for($this->user)->create([
        'description' => 'recurring transfer 5',
        'next_transaction_at' => now()->setDay(5),
    ]);

    RecurringTransfer::factory()->for($this->user)->create([
        'description' => 'recurring transfer 10',
        'next_transaction_at' => now()->setDay(10),
    ]);
});

it('sorts recurring transfers list page using next_transaction_at by default', function () {
    RecurringTransfer::factory()->for($this->user)->create();

    $this->get(RecurringTransferResource::getUrl('index'))->assertSeeTextInOrder([
        'recurring transfer 5', 'recurring transfer 10', 'recurring transfer 15',
    ]);
});
