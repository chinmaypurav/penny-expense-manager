<?php

use App\Enums\AccountType;
use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Models\Account;
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
});

it('imports accounts', function () {
    $csv = UploadedFile::fake()->createWithContent(
        'accounts.csv',
        Str::of('name,account_type,initial_balance,initial_date')->newLine()
            ->append('Account 1', ',')
            ->append(AccountType::SAVINGS->value, ',')
            ->append('1000', ',')
            ->append('2025-04-01', ',')
            ->toString()
    );

    livewire(ListAccounts::class)
        ->callAction(ImportAction::class, [
            'file' => $csv,
        ]);

    $this->assertDatabaseHas(Account::class, [
        'user_id' => $this->user->id,
        'name' => 'Account 1',
        'account_type' => AccountType::SAVINGS,
        'initial_balance' => 1000,
        'initial_date' => '2025-04-01',
    ]);
});

it('records failed import of accounts', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $csv = UploadedFile::fake()->createWithContent(
        'accounts.csv',
        Str::of('name,account_type,initial_balance,initial_date')->newLine()
            ->append(Str::random(256), ',')
            ->append(AccountType::SAVINGS->value, ',')
            ->append('1000', ',')
            ->append('2025-04-01', ',')
            ->toString()
    );

    livewire(ListAccounts::class)
        ->callAction(ImportAction::class, [
            'file' => $csv,
        ]);

    $this->assertDatabaseCount(FailedImportRow::class, 1);

    $this->assertDatabaseEmpty(Account::class);
});
