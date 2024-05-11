<?php

use App\Filament\Resources\ExpenseResource;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Person;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render expenses list page', function () {
    Expense::factory()->for($this->user)->create();

    $this->get(ExpenseResource::getUrl('index'))->assertSuccessful();
});

it('can create expense', function () {

    $newData = Expense::factory()->make();
    $account = Account::factory()->create();
    $person = Person::factory()->create();

    livewire(ExpenseResource\Pages\CreateExpense::class)
        ->fillForm([
            'description' => $newData->description,
            'person_id' => $person->id,
            'account_id' => $account->id,
            'amount' => $newData->amount,
            'transacted_at' => $newData->transacted_at,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Expense::class, [
        'description' => $newData->description,
        'person_id' => $person->id,
        'account_id' => $account->id,
        'amount' => $newData->amount,
        'transacted_at' => $newData->transacted_at,
    ]);
});

it('can render expense edit page', function () {
    $this->get(ExpenseResource::getUrl('edit', [
        'record' => Expense::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve expense data', function () {
    $expense = Expense::factory()->create();

    livewire(ExpenseResource\Pages\EditExpense::class, [
        'record' => $expense->getRouteKey(),
    ])
        ->assertFormSet([
            'description' => $expense->description,
            'person_id' => $expense->person_id,
            'account_id' => $expense->account_id,
            'amount' => $expense->amount,
            'transacted_at' => $expense->transacted_at,
        ]);
});

it('can update expense', function () {

    $expense = Expense::factory()->create();

    $person = Person::factory()->create();
    $account = Account::factory()->create();
    $newData = Expense::factory()->make();

    livewire(ExpenseResource\Pages\EditExpense::class, [
        'record' => $expense->getRouteKey(),
    ])
        ->fillForm([
            'description' => $newData->description,
            'person_id' => $person->id,
            'account_id' => $account->id,
            'amount' => $newData->amount,
            'transacted_at' => $newData->transacted_at,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Expense::class, [
        'description' => $newData->description,
        'person_id' => $person->id,
        'account_id' => $account->id,
        'amount' => $newData->amount,
        'transacted_at' => $newData->transacted_at,
    ]);
});

it('can delete expense', function () {

    $expense = Expense::factory()->for($this->user)->create();

    livewire(ExpenseResource\Pages\EditExpense::class, [
        'record' => $expense->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($expense);
});
