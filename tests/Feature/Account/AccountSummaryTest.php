<?php

use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can summarize account balance total', function () {
    Account::factory()->for($this->user)->createQuietly(['current_balance' => 1000]);
    Account::factory()->for($this->user)->createQuietly(['current_balance' => 2000]);

    livewire(ListAccounts::class)
        ->assertTableColumnSummarySet('current_balance', 'sum', 3000);
});
