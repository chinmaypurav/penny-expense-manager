<?php

namespace App\Filament\Schemas;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

abstract class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->state(fn (Expense|Income|null $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                TextEntry::make('updated_at')
                    ->label('Last Modified Date')
                    ->state(fn (Expense|Income|null $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                Select::make('person_id')
                    ->relationship(
                        'person',
                        'name'
                    )
                    ->nullable()
                    ->preload()
                    ->searchable(),

                Select::make('account_id')
                    ->relationship(
                        'account',
                        'name',
                        fn (Builder $query): Builder => $query->where('user_id', auth()->id())
                    )
                    ->getOptionLabelFromRecordUsing(fn (Account $record): string => $record->label)
                    ->required()
                    ->preload()
                    ->searchable(),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->preload()
                    ->searchable(),

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
