<?php

use App\Filament\Resources\IncomeResource;
use App\Models\Account;
use App\Models\Category;
use App\Models\Income;
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

it('can render incomes list page', function () {
    Income::factory()->for($this->user)->create();

    $this->get(IncomeResource::getUrl('index'))->assertSuccessful();
});

it('can create income', function () {

    $newData = Income::factory()->make();
    $account = Account::factory()->create();
    $person = Person::factory()->create();
    $category = Category::factory()->create();

    livewire(IncomeResource\Pages\CreateIncome::class)
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

    $this->assertDatabaseHas(Income::class, [
        'description' => $newData->description,
        'person_id' => $person->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => $newData->amount,
        'transacted_at' => $newData->transacted_at,
    ]);
});

it('can render income edit page', function () {
    $this->get(IncomeResource::getUrl('edit', [
        'record' => Income::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve income data', function () {
    $income = Income::factory()->create();

    livewire(IncomeResource\Pages\EditIncome::class, [
        'record' => $income->getRouteKey(),
    ])
        ->assertFormSet([
            'description' => $income->description,
            'person_id' => $income->person_id,
            'account_id' => $income->account_id,
            'category_id' => $income->category_id,
            'amount' => $income->amount,
            'transacted_at' => $income->transacted_at,
        ]);
});

it('can update income', function () {

    $income = Income::factory()->create();

    $person = Person::factory()->create();
    $account = Account::factory()->create();
    $newData = Income::factory()->make();
    $category = Category::factory()->create();

    livewire(IncomeResource\Pages\EditIncome::class, [
        'record' => $income->getRouteKey(),
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

    $this->assertDatabaseHas(Income::class, [
        'description' => $newData->description,
        'person_id' => $person->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => $newData->amount,
        'transacted_at' => $newData->transacted_at,
    ]);
});

it('can delete income', function () {

    $income = Income::factory()->for($this->user)->create();

    livewire(IncomeResource\Pages\EditIncome::class, [
        'record' => $income->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($income);
});
