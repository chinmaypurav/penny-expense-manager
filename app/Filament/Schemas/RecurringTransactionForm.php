<?php

namespace App\Filament\Schemas;

use App\Enums\Frequency;
use App\Models\RecurringExpense;
use App\Models\RecurringIncome;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

abstract class RecurringTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Select::make('account_id')
                    ->relationship(
                        'account',
                        'name',
                        fn (Builder $query): Builder => $query->where('user_id', auth()->id())
                    )
                    ->nullable()
                    ->preload()
                    ->searchable(),

                Select::make('person_id')
                    ->relationship('person', 'name')
                    ->preload()
                    ->searchable(),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->preload()
                    ->searchable(),

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
                    ->state(fn (RecurringIncome|RecurringExpense|null $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                TextEntry::make('updated_at')
                    ->label('Last Modified Date')
                    ->state(fn (RecurringIncome|RecurringExpense|null $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
