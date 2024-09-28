<?php

use App\Filament\Resources\ExpenseResource;
use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Person;
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

it('cannot add future date as transacted_at', function () {
    $newData = Expense::factory()->tomorrow()->make();
    $account = Account::factory()->create();
    $person = Person::factory()->create();
    $category = Category::factory()->create();

    livewire(ExpenseResource\Pages\CreateExpense::class)
        ->fillForm([
            'description' => $newData->description,
            'person_id' => $person->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => $newData->amount,
            'transacted_at' => $newData->transacted_at,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'transacted_at',
        ]);
});

it('cannot update future date as transacted_at', function () {
    $expense = Expense::factory()->for($this->user)->create();

    livewire(ExpenseResource\Pages\EditExpense::class, [
        'record' => $expense->getRouteKey(),
    ])
        ->fillForm([
            'transacted_at' => today()->addDay(),
        ])
        ->call('save')
        ->assertHasFormErrors([
            'transacted_at',
        ]);
});
