<?php

namespace App\Filament\Concerns;

use App\Models\Expense;
use App\Models\Income;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait IncomeExpenseResourceTrait
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (Expense|Income|null $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (Expense|Income|null $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('person.name'),

                TextColumn::make('account.name'),

                TextColumn::make('description')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('transacted_at')
                    ->label('Transacted Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('amount')
                    ->money('INR')
                    ->alignRight()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('transacted_at')
                    ->form([
                        DatePicker::make('transacted_from')->default(now()->startOfMonth()),
                        DatePicker::make('transacted_until')->default(now()->endOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['transacted_from'],
                                fn (Builder $query, $date): Builder => $query
                                    ->whereDate('transacted_at', '>=', $date),
                            )
                            ->when(
                                $data['transacted_until'],
                                fn (Builder $query, $date): Builder => $query
                                    ->whereDate('transacted_at', '<=', $date),
                            );
                    }),
                SelectFilter::make('labels')
                    ->relationship('labels', 'name')
                    ->multiple()
                    ->preload(),
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

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'person', 'account']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'person.name', 'account.name'];
    }

    public static function getGlobalSearchResultDetails(Model|Income|Expense $record): array
    {
        $details = [];

        if ($record->user) {
            $details['User'] = $record->user->name;
        }

        if ($record->person) {
            $details['Person'] = $record->person->name;
        }

        if ($record->account) {
            $details['Account'] = $record->account->name;
        }

        return $details;
    }
}
