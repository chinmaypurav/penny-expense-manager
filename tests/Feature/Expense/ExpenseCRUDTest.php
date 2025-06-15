<?php

use App\Filament\Resources\ExpenseResource;
use App\Filament\Resources\ExpenseResource\Pages\CreateExpense;
use App\Filament\Resources\ExpenseResource\Pages\EditExpense;
use App\Models\Account;
use App\Models\Category;
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
    $category = Category::factory()->create();

    livewire(CreateExpense::class)
        ->fillForm([
            'description' => $newData->description,
            'person_id' => $person->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => $newData->amount,
            'transacted_at' => $newData->transacted_at,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Expense::class, [
        'description' => $newData->description,
        'person_id' => $person->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => $newData->amount,
        'transacted_at' => $newData->transacted_at,
    ]);
});

it('can render expense edit page', function () {
    $this->get(ExpenseResource::getUrl('edit', [
        'record' => Expense::factory()->for($this->user)->create(),
    ]))->assertSuccessful();
});

it('can retrieve expense data', function () {
    $expense = Expense::factory()->for($this->user)->create();

    livewire(EditExpense::class, [
        'record' => $expense->getRouteKey(),
    ])
        ->assertFormSet([
            'description' => $expense->description,
            'person_id' => $expense->person_id,
            'account_id' => $expense->account_id,
            'category_id' => $expense->category_id,
            'amount' => $expense->amount,
            'transacted_at' => $expense->transacted_at,
        ]);
});

it('can update expense', function () {

    $expense = Expense::factory()->for($this->user)->create();

    $person = Person::factory()->create();
    $account = Account::factory()->create();
    $newData = Expense::factory()->make();
    $category = Category::factory()->create();

    livewire(EditExpense::class, [
        'record' => $expense->getRouteKey(),
    ])
        ->fillForm([
            'description' => $newData->description,
            'person_id' => $person->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => $newData->amount,
            'transacted_at' => $newData->transacted_at,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Expense::class, [
        'description' => $newData->description,
        'person_id' => $person->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => $newData->amount,
        'transacted_at' => $newData->transacted_at,
    ]);
});

it('can delete expense', function () {

    $expense = Expense::factory()->for($this->user)->create();

    livewire(EditExpense::class, [
        'record' => $expense->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($expense);
});
