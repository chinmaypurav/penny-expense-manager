<?php

use App\Enums\PanelId;
use App\Filament\Resources\IncomeResource\Pages\CreateIncome;
use App\Models\Account;
use App\Models\Category;
use App\Models\Person;
use App\Models\Tag;
use App\Models\User;
use Filament\Forms\Components\Select;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    PanelId::APP->setCurrentPanel();
});

it('displays only current user accounts filter', function () {
    $u1a1 = Account::factory()->for($this->user)->create(['name' => 'u1a1']);
    Account::factory()->for(User::factory())->create(['name' => 'u2a2']);

    livewire(CreateIncome::class)
        ->assertFormFieldExists(
            'account_id',
            checkFieldUsing: fn (Select $field) => $field->getOptions() === [$u1a1->id => $u1a1->label]
        );
});

it('displays people filter', function () {
    $person = Person::factory()
        ->create();

    livewire(CreateIncome::class)
        ->assertFormFieldExists(
            'person_id',
            checkFieldUsing: fn (Select $field) => $field->getOptions() === [$person->id => $person->name]
        );
});

it('displays category filter', function () {
    $category = Category::factory()
        ->create();

    livewire(CreateIncome::class)
        ->assertFormFieldExists(
            'category_id',
            checkFieldUsing: fn (Select $field) => $field->getOptions() === [$category->id => $category->name]
        );
});

it('displays tags filter', function () {
    $tag = Tag::factory()
        ->create();

    livewire(CreateIncome::class)
        ->assertFormFieldExists(
            'tags',
            checkFieldUsing: fn (Select $field) => $field->getOptions() === [$tag->id => $tag->name]
        );
});
