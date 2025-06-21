<?php

namespace App\Filament\Resources\TransferResource;

use App\Models\Account;
use App\Models\Transfer;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TransferForm
{
    public static function configure(Schema $schema): Schema
    {
        $accounts = Account::query()
            ->get()
            ->keyBy('id')
            ->map(fn (Account $account) => $account->label);

        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->state(fn (?Transfer $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                TextEntry::make('updated_at')
                    ->label('Last Modified Date')
                    ->state(fn (?Transfer $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                Select::make('creditor_id')
                    ->label('Creditor account')
                    ->helperText('The account where money is going to')
                    ->options($accounts)
                    ->searchable()
                    ->different('debtor_id')
                    ->disabledOn('edit')
                    ->required(),

                Select::make('debtor_id')
                    ->label('Debtor account')
                    ->helperText('The account where money is coming from')
                    ->options($accounts)
                    ->searchable()
                    ->different('creditor_id')
                    ->disabledOn('edit')
                    ->required(),

                TextInput::make('description')
                    ->required(),

                DateTimePicker::make('transacted_at')
                    ->label('Transacted Date')
                    ->default(now())
                    ->maxDate(now())
                    ->required(),

                TextInput::make('amount')
                    ->required()
                    ->numeric(),

                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
            ]);
    }
}
