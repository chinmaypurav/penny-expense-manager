<?php

use App\Filament\Resources\RecurringIncomeResource;
use App\Filament\Resources\RecurringIncomeResource\Pages\CreateRecurringIncome;
use App\Filament\Resources\RecurringIncomeResource\Pages\EditRecurringIncome;
use App\Models\Account;
use App\Models\Category;
use App\Models\Person;
use App\Models\RecurringIncome;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render recurring recurring incomes list page', function () {
    RecurringIncome::factory()->for($this->user)->create();

    $this->get(RecurringIncomeResource::getUrl('index'))->assertSuccessful();
});

it('can create recurring income', function () {

    $newData = RecurringIncome::factory()->make();
    $account = Account::factory()->create();
    $person = Person::factory()->create();
    $category = Category::factory()->create();

    livewire(CreateRecurringIncome::class)
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

    $this->assertDatabaseHas(RecurringIncome::class, [
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

it('can render recurring income edit page', function () {
    $this->get(RecurringIncomeResource::getUrl('edit', [
        'record' => RecurringIncome::factory()->for($this->user)->create(),
    ]))->assertSuccessful();
});

it('can retrieve recurring income data', function () {
    $recurringIncome = RecurringIncome::factory()->for($this->user)->create();

    livewire(EditRecurringIncome::class, [
        'record' => $recurringIncome->getRouteKey(),
    ])
        ->assertFormSet([
            'description' => $recurringIncome->description,
            'person_id' => $recurringIncome->person_id,
            'account_id' => $recurringIncome->account_id,
            'amount' => $recurringIncome->amount,
            'next_transaction_at' => $recurringIncome->next_transaction_at->toDateString(),
            'remaining_recurrences' => $recurringIncome->remaining_recurrences,
            'frequency' => $recurringIncome->frequency->value,
        ]);
});

it('can update recurring income', function () {

    $recurringIncome = RecurringIncome::factory()->for($this->user)->create();

    $person = Person::factory()->create();
    $account = Account::factory()->create();
    $category = Category::factory()->create();
    $newData = RecurringIncome::factory()->make();

    livewire(EditRecurringIncome::class, [
        'record' => $recurringIncome->getRouteKey(),
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

    $this->assertDatabaseHas(RecurringIncome::class, [
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

it('can delete recurring income', function () {

    $recurringIncome = RecurringIncome::factory()->for($this->user)->create();

    livewire(EditRecurringIncome::class, [
        'record' => $recurringIncome->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($recurringIncome);
});
