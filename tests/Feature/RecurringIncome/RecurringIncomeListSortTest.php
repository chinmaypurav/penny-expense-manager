<?php

use App\Filament\Resources\RecurringIncomeResource;
use App\Models\RecurringIncome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\travelTo;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    travelTo(now()->setDay(20)); // ensure default monthly filter does not exclude results

    RecurringIncome::factory()->for($this->user)->create([
        'description' => 'recurring income 15',
        'next_transaction_at' => now()->setDay(15),
    ]);

    RecurringIncome::factory()->for($this->user)->create([
        'description' => 'recurring income 5',
        'next_transaction_at' => now()->setDay(5),
    ]);

    RecurringIncome::factory()->for($this->user)->create([
        'description' => 'recurring income 10',
        'next_transaction_at' => now()->setDay(10),
    ]);
});

it('sorts recurring incomes list page using next_transaction_at by default', function () {
    RecurringIncome::factory()->for($this->user)->create();

    $this->get(RecurringIncomeResource::getUrl('index'))->assertSeeTextInOrder([
        'recurring income 5', 'recurring income 10', 'recurring income 15',
    ]);
});
