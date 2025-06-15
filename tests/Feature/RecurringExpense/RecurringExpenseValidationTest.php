<?php

use App\Filament\Resources\RecurringExpenseResource\Pages\CreateRecurringExpense;
use App\Filament\Resources\RecurringExpenseResource\Pages\EditRecurringExpense;
use App\Models\Account;
use App\Models\Category;
use App\Models\Person;
use App\Models\RecurringExpense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can have some null and some required fields', function () {
    livewire(CreateRecurringExpense::class)
        ->call('create')
        ->assertHasNoFormErrors([
            'account_id',
            'category_id',
            'person_id',
            'remaining_recurrences',
            'tags',
        ])
        ->assertHasFormErrors([
            'description',
            'amount',
            'next_transaction_at',
            'frequency',
        ]);
});

it('cannot add past date as next transaction date', function () {
    $newData = RecurringExpense::factory()->make([
        'next_transaction_at' => today(),
    ]);
    $account = Account::factory()->create();
    $person = Person::factory()->create();
    $category = Category::factory()->create();

    livewire(CreateRecurringExpense::class)
        ->fillForm([
            'description' => $newData->description,
            'person_id' => $person->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => $newData->amount,
            'next_transaction_at' => $newData->next_transaction_at,
            'remaining_recurrences' => $newData->remaining_recurrences,
            'frequency' => $newData->frequency,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'next_transaction_at',
        ]);
});

it('cannot update past date as next transaction date', function () {

    $recurringExpense = RecurringExpense::factory()->for($this->user)->create();

    livewire(EditRecurringExpense::class, [
        'record' => $recurringExpense->getRouteKey(),
    ])
        ->fillForm([
            'next_transaction_at' => today(),
        ])
        ->call('save')
        ->assertHasFormErrors([
            'next_transaction_at',
        ]);
});
