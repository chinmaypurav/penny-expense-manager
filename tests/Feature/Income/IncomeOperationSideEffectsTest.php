<?php

use App\Filament\Resources\IncomeResource;
use App\Models\Account;
use App\Models\Income;
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

it('adds account balance when created', function () {

    $newData = Income::factory()->make([
        'amount' => 3000,
    ]);

    $account = Account::factory()->create([
        'balance' => 1000,
    ]);
    $person = Person::factory()->create();

    livewire(IncomeResource\Pages\CreateIncome::class)
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
        'balance' => 4000,
    ]);
});

it('adjusts account balance when updated', function (int $incomeAmount, int $accountBalance) {

    $account = Account::factory()->create([
        'balance' => 10_000,
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
            'transacted_at' => $income->transacted_at,

            'amount' => $incomeAmount,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'balance' => $accountBalance,
    ]);
})->with([
    'decrease' => [2000, 8000],
    'increase' => [6000, 12000],
]);

it('subtracts account balance when removed', function () {
    $account = Account::factory()->create([
        'balance' => 1000,
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
        'balance' => -2000,
    ]);
});