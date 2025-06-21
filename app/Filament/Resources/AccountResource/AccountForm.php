<?php

namespace App\Filament\Resources\AccountResource;

use App\Enums\AccountType;
use App\Models\Account;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->state(fn (?Account $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                TextEntry::make('updated_at')
                    ->label('Last Modified Date')
                    ->state(fn (?Account $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                TextInput::make('name')
                    ->unique(ignorable: $schema->getRecord())
                    ->required()
                    ->columnSpan(fn (string $operation): int => $operation === 'create' ? 1 : 2),

                Select::make('account_type')
                    ->disabledOn('edit')
                    ->required()
                    ->options(AccountType::class),

                TextInput::make('current_balance')
                    ->label('Current Balance')
                    ->hiddenOn(['create'])
                    ->readOnly(),

                DatePicker::make('initial_date')
                    ->label('Initial Balance Date')
                    ->default(today())
                    ->beforeOrEqual(today())
                    ->required(),

                TextInput::make('initial_balance')
                    ->label('Initial Balance')
                    ->required()
                    ->numeric(),
            ]);
    }
}
