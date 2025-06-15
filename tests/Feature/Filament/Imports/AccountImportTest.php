<?php

use App\Enums\AccountType;
use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Models\Account;
use App\Models\User;
use Filament\Actions\ImportAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('imports accounts', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $csv = UploadedFile::fake()->createWithContent(
        'accounts.csv',
        Str::of('name,account_type,initial_balance,initial_date')->newLine()
            ->append('Account 1,savings,1000,2025-04-01')->toString()
    );

    livewire(ListAccounts::class)
        ->callAction(ImportAction::class, [
            'file' => $csv,
        ]);

    $this->assertDatabaseHas(Account::class, [
        'user_id' => 1,
        'name' => 'Account 1',
        'account_type' => AccountType::SAVINGS,
        'initial_balance' => 1000,
        'initial_date' => '2025-04-01',
    ]);
});
