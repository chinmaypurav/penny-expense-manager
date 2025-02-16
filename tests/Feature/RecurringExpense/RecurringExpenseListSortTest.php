<?php

use App\Filament\Resources\RecurringExpenseResource;
use App\Models\RecurringExpense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\travelTo;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    travelTo(now()->setDay(20)); // ensure default monthly filter does not exclude results

    RecurringExpense::factory()->for($this->user)->create([
        'description' => 'recurring expense 15',
        'next_transaction_at' => now()->setDay(15),
    ]);

    RecurringExpense::factory()->for($this->user)->create([
        'description' => 'recurring expense 5',
        'next_transaction_at' => now()->setDay(5),
    ]);

    RecurringExpense::factory()->for($this->user)->create([
        'description' => 'recurring expense 10',
        'next_transaction_at' => now()->setDay(10),
    ]);
});

it('sorts recurring expenses list page using next_transaction_at by default', function () {
    RecurringExpense::factory()->for($this->user)->create();

    $this->get(RecurringExpenseResource::getUrl('index'))->assertSeeTextInOrder([
        'recurring expense 5', 'recurring expense 10', 'recurring expense 15',
    ]);
});
