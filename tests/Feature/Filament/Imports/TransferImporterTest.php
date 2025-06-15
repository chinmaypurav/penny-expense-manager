<?php

use App\Filament\Resources\TransferResource\Pages\ListTransfers;
use App\Models\Account;
use App\Models\Transfer;
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

    $this->creditor = Account::factory()->for($this->user)->create(['name' => 'Creditor Account']);
    $this->debtor = Account::factory()->for($this->user)->create(['name' => 'Debtor Account']);
});

it('imports transfers', function () {
    $csv = UploadedFile::fake()->createWithContent(
        'transfers.csv',
        Str::of('creditor,debtor,description,transacted_at,amount')->newLine()
            ->append($this->creditor->name, ',')
            ->append($this->debtor->name, ',')
            ->append('Transfer 1', ',')
            ->append('2025-04-01 00:00:00', ',')
            ->append('1000')->newLine()
            ->toString()
    );

    livewire(ListTransfers::class)
        ->callAction(ImportAction::class, [
            'file' => $csv,
        ]);

    $this->assertDatabaseHas(Transfer::class, [
        'user_id' => $this->user->id,
        'creditor_id' => $this->creditor->id,
        'debtor_id' => $this->debtor->id,
        'description' => 'Transfer 1',
        'transacted_at' => '2025-04-01 00:00:00',
        'amount' => 1000,
    ]);
});

it('records failed import of transfers', function () {
    $csv = UploadedFile::fake()->createWithContent(
        'transfers.csv',
        Str::of('creditor,debtor,description,transacted_at,amount')->newLine()
            ->append($this->creditor->name, ',')
            ->append($this->debtor->name, ',')
            ->append(Str::random(256), ',')
            ->append('2025-04-01 00:00:00', ',')
            ->append('1000')->newLine()
            ->toString()
    );

    livewire(ListTransfers::class)
        ->callAction(ImportAction::class, [
            'file' => $csv,
        ]);

    $this->assertDatabaseCount(FailedImportRow::class, 1);

    $this->assertDatabaseEmpty(Transfer::class);
});
