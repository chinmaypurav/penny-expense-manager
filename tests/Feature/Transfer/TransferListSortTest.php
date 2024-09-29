<?php

use App\Filament\Resources\TransferResource;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Carbon::setTestNow(Carbon::now()->setDay(20)); // ensure default monthly filter does not exclude results

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

it('sorts expenses list page using transacted_at by default', function () {
    Transfer::factory()->for($this->user)->create();

    $this->get(TransferResource::getUrl('index'))->assertSeeTextInOrder([
        'transfer 5', 'transfer 10', 'transfer 15',
    ]);
});