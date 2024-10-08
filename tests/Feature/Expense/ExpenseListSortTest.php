<?php

use App\Filament\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Carbon::setTestNow(Carbon::now()->setDay(20)); // ensure default monthly filter does not exclude results

    Expense::factory()->for($this->user)->create([
        'description' => 'expense 15',
        'transacted_at' => now()->setDay(15),
    ]);

    Expense::factory()->for($this->user)->create([
        'description' => 'expense 5',
        'transacted_at' => now()->setDay(5),
    ]);

    Expense::factory()->for($this->user)->create([
        'description' => 'expense 10',
        'transacted_at' => now()->setDay(10),
    ]);
});

it('sorts expenses list page using transacted_at by default', function () {
    Expense::factory()->for($this->user)->create();

    $this->get(ExpenseResource::getUrl('index'))->assertSeeTextInOrder([
        'expense 5', 'expense 10', 'expense 15',
    ]);
});
