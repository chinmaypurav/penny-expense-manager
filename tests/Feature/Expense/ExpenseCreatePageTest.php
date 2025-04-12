<?php

use App\Enums\PanelId;
use App\Filament\Resources\ExpenseResource\Pages\CreateExpense;
use App\Models\Account;
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

    livewire(CreateExpense::class)
        ->assertFormFieldExists(
            'account_id',
            checkFieldUsing: fn (Select $field) => $field->getOptions() === [$u1a1->id => $u1a1->name]
        );
});
