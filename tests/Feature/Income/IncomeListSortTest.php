<?php

use App\Filament\Resources\IncomeResource;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\travelTo;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    travelTo(now()->setDay(20)); // ensure default monthly filter does not exclude results

    Income::factory()->for($this->user)->create([
        'description' => 'income 15',
        'transacted_at' => now()->setDay(15),
    ]);

    Income::factory()->for($this->user)->create([
        'description' => 'income 5',
        'transacted_at' => now()->setDay(5),
    ]);

    Income::factory()->for($this->user)->create([
        'description' => 'income 10',
        'transacted_at' => now()->setDay(10),
    ]);
});

it('sorts incomes list page using transacted_at desc by default', function () {
    Income::factory()->for($this->user)->create();

    $this->get(IncomeResource::getUrl('index'))->assertSeeTextInOrder([
        'income 15', 'income 10', 'income 5',
    ]);
});
