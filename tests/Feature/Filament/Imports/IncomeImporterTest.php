<?php

use App\Filament\Resources\IncomeResource\Pages\ListIncomes;
use App\Models\Account;
use App\Models\Category;
use App\Models\Income;
use App\Models\Person;
use App\Models\User;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Models\FailedImportRow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->person = Person::factory()->create();
    $this->category = Category::factory()->create();
    $this->account = Account::factory()->for($this->user)->create();
});

it('imports incomes', function () {
    $csv = UploadedFile::fake()->createWithContent(
        'incomes.csv',
        Str::of('person,account,category,description,transacted_at,amount')->newLine()
            ->append($this->person->name, ',')
            ->append($this->account->name, ',')
            ->append($this->category->name, ',')
            ->append('Income 1', ',')
            ->append('2025-04-01 00:00:00', ',')
            ->append('1000')->newLine()
            ->toString()
    );

    livewire(ListIncomes::class)
        ->callAction(ImportAction::class, [
            'file' => $csv,
        ]);

    $this->assertDatabaseHas(Income::class, [
        'user_id' => $this->user->id,
        'person_id' => $this->person->id,
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'description' => 'Income 1',
        'transacted_at' => '2025-04-01 00:00:00',
        'amount' => 1000,
    ]);
});

it('records failed import of incomes', function () {
    $csv = UploadedFile::fake()->createWithContent(
        'incomes.csv',
        Str::of('person,account,category,description,transacted_at,amount')->newLine()
            ->append($this->person->name, ',')
            ->append($this->account->name, ',')
            ->append($this->category->name, ',')
            ->append(Str::random(256), ',')
            ->append('2025-04-01 00:00:00', ',')
            ->append('1000')->newLine()
            ->toString()
    );

    livewire(ListIncomes::class)
        ->callAction(ImportAction::class, [
            'file' => $csv,
        ]);

    $this->assertDatabaseCount(FailedImportRow::class, 1);

    $this->assertDatabaseEmpty(Income::class);
});
