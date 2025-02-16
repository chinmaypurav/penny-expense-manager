<?php

use App\Filament\Resources\IncomeResource;
use App\Models\Account;
use App\Models\Income;
use App\Models\Person;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Laravel\freezeTime;
use function Pest\Livewire\livewire;

uses(DatabaseMigrations::class);

beforeEach(function () {
    freezeTime();

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('adds account current_balance when created', function () {

    $newData = Income::factory()->make([
        'amount' => 3000,
    ]);

    $account = Account::factory()->create([
        'current_balance' => 1000,
    ]);
    $person = Person::factory()->create();

    livewire(IncomeResource\Pages\CreateIncome::class)
        ->fillForm([
            'description' => $newData->description,
            'person_id' => $person->id,
            'account_id' => $account->id,
            'category_id' => $newData->category_id,
            'amount' => $newData->amount,
            'transacted_at' => $newData->transacted_at,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'current_balance' => 4000,
    ]);
});

it('adjusts account current_balance when updated', function (int $incomeAmount, int $accountBalance) {

    $account = Account::factory()->create([
        'current_balance' => 10_000,
    ]);

    $income = Income::factory()
        ->for($this->user)
        ->for($account)
        ->createQuietly([
            'amount' => 4000,
        ]);

    livewire(IncomeResource\Pages\EditIncome::class, [
        'record' => $income->getKey(),
    ])
        ->fillForm([
            'description' => $income->description,
            'person_id' => $income->person_id,
            'account_id' => $income->account_id,
            'category_id' => $income->category_id,
            'transacted_at' => $income->transacted_at,

            'amount' => $incomeAmount,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'current_balance' => $accountBalance,
    ]);
})->with([
    'decrease' => [2000, 8000],
    'increase' => [6000, 12000],
]);

it('subtracts account current_balance when removed', function () {
    $account = Account::factory()->create([
        'current_balance' => 1000,
    ]);
    $income = Income::factory()
        ->for($this->user)
        ->for($account)
        ->createQuietly([
            'amount' => 3000,
        ]);

    livewire(IncomeResource\Pages\EditIncome::class, [
        'record' => $income->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'current_balance' => -2000,
    ]);
});
