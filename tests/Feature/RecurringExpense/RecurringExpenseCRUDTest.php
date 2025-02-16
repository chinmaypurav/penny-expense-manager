<?php

use App\Filament\Resources\RecurringExpenseResource;
use App\Models\Account;
use App\Models\Category;
use App\Models\Person;
use App\Models\RecurringExpense;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render recurring recurring expenses list page', function () {
    RecurringExpense::factory()->for($this->user)->create();

    $this->get(RecurringExpenseResource::getUrl('index'))->assertSuccessful();
});

it('can create recurring expense', function () {

    $newData = RecurringExpense::factory()->make();
    $account = Account::factory()->create();
    $person = Person::factory()->create();
    $category = Category::factory()->create();

    livewire(RecurringExpenseResource\Pages\CreateRecurringExpense::class)
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
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(RecurringExpense::class, [
        'description' => $newData->description,
        'person_id' => $person->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => $newData->amount,
        'next_transaction_at' => $newData->next_transaction_at->toDateString(),
        'remaining_recurrences' => $newData->remaining_recurrences,
        'frequency' => $newData->frequency,
    ]);
});

it('can render recurring expense edit page', function () {
    $this->get(RecurringExpenseResource::getUrl('edit', [
        'record' => RecurringExpense::factory()->for($this->user)->create(),
    ]))->assertSuccessful();
});

it('can retrieve recurring expense data', function () {
    $recurringExpense = RecurringExpense::factory()->for($this->user)->create();

    livewire(RecurringExpenseResource\Pages\EditRecurringExpense::class, [
        'record' => $recurringExpense->getRouteKey(),
    ])
        ->assertFormSet([
            'description' => $recurringExpense->description,
            'person_id' => $recurringExpense->person_id,
            'account_id' => $recurringExpense->account_id,
            'amount' => $recurringExpense->amount,
            'next_transaction_at' => $recurringExpense->next_transaction_at->toDateString(),
            'remaining_recurrences' => $recurringExpense->remaining_recurrences,
            'frequency' => $recurringExpense->frequency->value,
        ]);
});

it('can update recurring expense', function () {

    $recurringExpense = RecurringExpense::factory()->for($this->user)->create();

    $person = Person::factory()->create();
    $account = Account::factory()->create();
    $category = Category::factory()->create();
    $newData = RecurringExpense::factory()->make();

    livewire(RecurringExpenseResource\Pages\EditRecurringExpense::class, [
        'record' => $recurringExpense->getRouteKey(),
    ])
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
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(RecurringExpense::class, [
        'description' => $newData->description,
        'person_id' => $person->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => $newData->amount,
        'next_transaction_at' => $newData->next_transaction_at->toDateString(),
        'remaining_recurrences' => $newData->remaining_recurrences,
        'frequency' => $newData->frequency,
    ]);
});

it('can delete recurring expense', function () {

    $recurringExpense = RecurringExpense::factory()->for($this->user)->create();

    livewire(RecurringExpenseResource\Pages\EditRecurringExpense::class, [
        'record' => $recurringExpense->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($recurringExpense);
});
