<?php

use App\Enums\PanelId;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
use App\Models\User;
use Filament\Livewire\GlobalSearch;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $user1 = User::factory()->create();
    $this->actingAs($user1);
    $user2 = User::factory()->create();

    Account::factory()->for($user1)->create(['name' => 'u1a1']);
    Account::factory()->for($user1)->create(['name' => 'u1a2']);
    Account::factory()->for($user2)->create(['name' => 'u2a1']);
    Account::factory()->for($user2)->create(['name' => 'u2a2']);

    Income::factory()->for($user1)->create(['description' => 'u1i1']);
    Income::factory()->for($user1)->create(['description' => 'u1i2']);
    Income::factory()->for($user2)->create(['description' => 'u2i1']);
    Income::factory()->for($user2)->create(['description' => 'u2i2']);

    Expense::factory()->for($user1)->create(['description' => 'u1e1']);
    Expense::factory()->for($user1)->create(['description' => 'u1e2']);
    Expense::factory()->for($user2)->create(['description' => 'u2e1']);
    Expense::factory()->for($user2)->create(['description' => 'u2e2']);

    Transfer::factory()->for($user1)->create(['description' => 'u1t1']);
    Transfer::factory()->for($user1)->create(['description' => 'u1t2']);
    Transfer::factory()->for($user2)->create(['description' => 'u2t1']);
    Transfer::factory()->for($user2)->create(['description' => 'u2t2']);

    PanelId::FAMILY->setCurrentPanel();
});

it('returns all user results', function (string $search, string $attribute, array $results) {
    livewire(GlobalSearch::class)
        ->set('search', $search)
        ->assertSee($results);
})->with([
    'income' => ['i1', 'description', ['u1i1', 'u2i1']],
    'expense' => ['e1', 'description', ['u1e1', 'u2e1']],
    'transfer' => ['t1', 'description', ['u1t1', 'u2t1']],
]);

it('returns all user results with partial keyword', function (string $search, array $results) {
    livewire(GlobalSearch::class)
        ->set('search', $search)
        ->assertSee($results);
})->with([
    'income' => ['i1', ['u1i1', 'u2i1']],
    'expense' => ['e1', ['u1e1', 'u2e1']],
    'transfer' => ['t1', ['u1t1', 'u2t1']],
]);
