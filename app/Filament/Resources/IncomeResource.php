<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\IncomeExpenseResourceTrait;
use App\Filament\Resources\IncomeResource\Pages;
use App\Models\Income;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class IncomeResource extends Resource
{
    use IncomeExpenseResourceTrait;

    protected static ?string $model = Income::class;

    protected static ?string $slug = 'incomes';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?Income $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?Income $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                Select::make('person_id')
                    ->relationship('person', 'name')
                    ->nullable(),

                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->required(),

                TextInput::make('description')
                    ->required(),

                DateTimePicker::make('transacted_at')
                    ->label('Transacted Date')
                    ->default(now()),

                TextInput::make('amount')
                    ->required()
                    ->numeric(),

                Select::make('labels')
                    ->relationship('labels', 'name')
                    ->multiple()
                    ->preload(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomes::route('/'),
            'create' => Pages\CreateIncome::route('/create'),
            'edit' => Pages\EditIncome::route('/{record}/edit'),
        ];
    }
}
