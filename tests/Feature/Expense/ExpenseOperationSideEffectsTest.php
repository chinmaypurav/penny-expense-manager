<?php

use App\Filament\Resources\ExpenseResource;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Person;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Livewire\livewire;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Carbon::setTestNow(today());

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('subtracts account balance when created', function () {

    $newData = Expense::factory()->make([
        'amount' => 3000,
    ]);

    $account = Account::factory()->create([
        'balance' => 1000,
    ]);
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

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'balance' => -2000,
    ]);
});

it('adjusts account balance when updated', function (int $expenseAmount, int $accountBalance) {

    $account = Account::factory()->create([
        'balance' => 10_000,
    ]);

    $expense = Expense::factory()
        ->for($this->user)
        ->for($account)
        ->createQuietly([
            'amount' => 4000,
        ]);

    livewire(ExpenseResource\Pages\EditExpense::class, [
        'record' => $expense->getKey(),
    ])
        ->fillForm([
            'description' => $expense->description,
            'person_id' => $expense->person_id,
            'account_id' => $expense->account_id,
            'transacted_at' => $expense->transacted_at,

            'amount' => $expenseAmount,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'balance' => $accountBalance,
    ]);
})->with([
    'decrease' => [6000, 8000],
    'increase' => [2000, 12000],
]);

it('adds account balance when removed', function () {
    $account = Account::factory()->create([
        'balance' => 1000,
    ]);
    $expense = Expense::factory()
        ->for($this->user)
        ->for($account)
        ->createQuietly([
            'amount' => 3000,
        ]);

    livewire(ExpenseResource\Pages\EditExpense::class, [
        'record' => $expense->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'balance' => 4000,
    ]);
});
