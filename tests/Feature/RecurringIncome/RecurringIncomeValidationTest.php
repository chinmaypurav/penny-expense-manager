<?php

use App\Filament\Resources\RecurringIncomeResource;
use App\Models\Account;
use App\Models\Category;
use App\Models\Person;
use App\Models\RecurringIncome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Carbon::setTestNow(now());
});

it('can have some null and some required fields', function () {
    livewire(RecurringIncomeResource\Pages\CreateRecurringIncome::class)
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

it('cannot add past past date as next transaction date', function () {
    $newData = RecurringIncome::factory()->make([
        'next_transaction_at' => today(),
    ]);
    $account = Account::factory()->create();
    $person = Person::factory()->create();
    $category = Category::factory()->create();

    livewire(RecurringIncomeResource\Pages\CreateRecurringIncome::class)
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

it('can update past date as next transaction date', function () {

    $recurringIncome = RecurringIncome::factory()->for($this->user)->create();

    livewire(RecurringIncomeResource\Pages\EditRecurringIncome::class, [
        'record' => $recurringIncome->getRouteKey(),
    ])
        ->fillForm([
            'next_transaction_at' => today(),
        ])
        ->call('save')
        ->assertHasFormErrors([
            'next_transaction_at',
        ]);
});
