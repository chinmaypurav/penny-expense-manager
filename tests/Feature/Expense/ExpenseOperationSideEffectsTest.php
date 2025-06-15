<?php

use App\Filament\Resources\ExpenseResource\Pages\CreateExpense;
use App\Filament\Resources\ExpenseResource\Pages\EditExpense;
use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Person;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Livewire\livewire;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('subtracts account current_balance when created', function () {

    $newData = Expense::factory()->make([
        'amount' => 3000,
    ]);

    $account = Account::factory()->create([
        'current_balance' => 1000,
    ]);
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

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'current_balance' => -2000,
    ]);
});

it('adjusts account current_balance when updated', function (int $expenseAmount, int $accountBalance) {

    $account = Account::factory()->create([
        'current_balance' => 10_000,
    ]);

    $expense = Expense::factory()
        ->for($this->user)
        ->for($account)
        ->createQuietly([
            'amount' => 4000,
        ]);

    livewire(EditExpense::class, [
        'record' => $expense->getKey(),
    ])
        ->fillForm([
            'description' => $expense->description,
            'person_id' => $expense->person_id,
            'account_id' => $expense->account_id,
            'category_id' => $expense->category_id,
            'transacted_at' => $expense->transacted_at,

            'amount' => $expenseAmount,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'current_balance' => $accountBalance,
    ]);
})->with([
    'decrease' => [6000, 8000],
    'increase' => [2000, 12000],
]);

it('adds account current_balance when removed', function () {
    $account = Account::factory()->create([
        'current_balance' => 1000,
    ]);
    $expense = Expense::factory()
        ->for($this->user)
        ->for($account)
        ->createQuietly([
            'amount' => 3000,
        ]);

    livewire(EditExpense::class, [
        'record' => $expense->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'current_balance' => 4000,
    ]);
});

it('doesnt affect account balances when amount and transacted at clean on expense update', function () {
    $account = Account::factory()->createQuietly([
        'current_balance' => 1000,
    ]);

    $expense = Expense::factory()
        ->for($this->user)
        ->for($account)
        ->createQuietly([
            'amount' => 3000,
        ]);

    $newData = Expense::factory()->make([
        'amount' => 3000,
    ]);

    livewire(EditExpense::class, [
        'record' => $expense->getRouteKey(),
    ])
        ->fillForm([
            'description' => $newData->description,
            'person_id' => $newData->person_id,
            'account_id' => $newData->account_id,
            'category_id' => $newData->category_id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'current_balance' => 1000,
    ]);
});
