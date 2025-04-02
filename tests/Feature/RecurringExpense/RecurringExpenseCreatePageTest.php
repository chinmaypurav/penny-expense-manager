<?php

use App\Enums\PanelId;
use App\Filament\Resources\RecurringExpenseResource\Pages\CreateRecurringExpense;
use App\Models\Account;
use App\Models\User;
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
    $u2a2 = Account::factory()->for(User::factory())->create(['name' => 'u2a2']);

    livewire(CreateRecurringExpense::class)
        ->assertSeeText($u1a1->name)
        ->assertDontSeeText($u2a2->name);
});
