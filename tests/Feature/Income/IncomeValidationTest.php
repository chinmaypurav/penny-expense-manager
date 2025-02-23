    <?php

use App\Filament\Resources\IncomeResource;
use App\Models\Account;
use App\Models\Category;
use App\Models\Income;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('cannot add future date as transacted_at', function () {
    $newData = Income::factory()->tomorrow()->make();
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
        ->assertHasFormErrors([
            'transacted_at',
        ]);
});

it('cannot update future date as transacted_at', function () {
    $income = Income::factory()->for($this->user)->create();

    livewire(IncomeResource\Pages\EditIncome::class, [
        'record' => $income->getRouteKey(),
    ])
        ->fillForm([
            'transacted_at' => today()->addDay(),
        ])
        ->call('save')
        ->assertHasFormErrors([
            'transacted_at',
        ]);
});
