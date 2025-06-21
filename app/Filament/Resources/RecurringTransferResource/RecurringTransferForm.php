<?php

namespace App\Filament\Resources\RecurringTransferResource;

use App\Enums\Frequency;
use App\Models\Account;
use App\Models\RecurringTransfer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RecurringTransferForm
{
    public static function configure(Schema $schema): Schema
    {
        $accounts = Account::query()
            ->pluck('name', 'id');

        return $schema
            ->components([
                Select::make('creditor_id')
                    ->label('Creditor account')
                    ->helperText('The account where money is going to')
                    ->options($accounts)
                    ->searchable()
                    ->different('debtor_id')
                    ->required(),

                Select::make('debtor_id')
                    ->label('Debtor account')
                    ->helperText('The account where money is coming from')
                    ->options($accounts)
                    ->searchable()
                    ->different('creditor_id')
                    ->required(),

                TextInput::make('description')
                    ->required(),

                TextInput::make('amount')
                    ->numeric()
                    ->required(),

                DatePicker::make('next_transaction_at')
                    ->label('Next Transaction Date')
                    ->required()
                    ->after(today()),

                Select::make('frequency')
                    ->options(Frequency::class)
                    ->required(),

                TextInput::make('remaining_recurrences')
                    ->integer()
                    ->helperText('Leave blank for infinite recurrences'),

                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),

                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->state(fn (?RecurringTransfer $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                TextEntry::make('updated_at')
                    ->label('Last Modified Date')
                    ->state(fn (?RecurringTransfer $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
