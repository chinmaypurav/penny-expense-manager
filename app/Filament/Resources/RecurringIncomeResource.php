<?php

namespace App\Filament\Resources;

use App\Enums\Frequency;
use App\Filament\Resources\RecurringIncomeResource\Pages;
use App\Models\RecurringIncome;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RecurringIncomeResource extends Resource
{
    protected static ?string $model = RecurringIncome::class;

    protected static ?string $slug = 'recurring-incomes';

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-up';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?RecurringIncome $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?RecurringIncome $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                TextInput::make('description')
                    ->required(),

                TextInput::make('amount')
                    ->numeric()
                    ->required(),

                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),

                Select::make('person_id')
                    ->relationship('person', 'name')
                    ->preload()
                    ->searchable(),

                DatePicker::make('next_transaction_at')
                    ->label('Next Transaction Date'),

                Select::make('frequency')
                    ->options(Frequency::all())
                    ->required(),

                TextInput::make('remaining_recurrences')
                    ->required()
                    ->integer(),

                Select::make('labels')
                    ->relationship('labels', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description'),

                TextColumn::make('amount'),

                TextColumn::make('account.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('person.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('next_transaction_at')
                    ->label('Next Transaction Date')
                    ->date(),

                TextColumn::make('frequency'),

                TextColumn::make('remaining_recurrences'),
            ])
            ->filters([
                SelectFilter::make('account')
                    ->relationship('account', 'name'),
                SelectFilter::make('person')
                    ->relationship('person', 'name'),
                SelectFilter::make('frequency')
                    ->options(Frequency::all()),
            ], FiltersLayout::AboveContentCollapsible)
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecurringIncomes::route('/'),
            'create' => Pages\CreateRecurringIncome::route('/create'),
            'edit' => Pages\EditRecurringIncome::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'account', 'person']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'account.name', 'person.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->user) {
            $details['User'] = $record->user->name;
        }

        if ($record->account) {
            $details['Account'] = $record->account->name;
        }

        if ($record->person) {
            $details['Person'] = $record->person->name;
        }

        return $details;
    }
}
