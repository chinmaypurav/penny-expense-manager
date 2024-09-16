<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnualStatementResource\Pages;
use App\Models\AnnualStatement;
use App\Models\FinancialYear;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AnnualStatementResource extends Resource
{
    protected static ?string $model = AnnualStatement::class;

    protected static ?string $slug = 'annual-statements';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?AnnualStatement $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?AnnualStatement $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),

                Select::make('financial_year_id')
                    ->relationship(
                        'financialYear',
                        modifyQueryUsing: fn (Builder $query) => $query->whereKeyNot(AnnualStatement::pluck('financial_year_id')))
                    ->getOptionLabelFromRecordUsing(fn (FinancialYear $record) => $record->start_date->year)
                    ->required(),

                TextInput::make('salary')
                    ->numeric()
                    ->label('Salary')
                    ->default(0),

                TextInput::make('rent')
                    ->numeric()
                    ->label('Rent')
                    ->default(0),

                TextInput::make('interest')
                    ->numeric()
                    ->label('Interest')
                    ->default(0),

                TextInput::make('ltcg')
                    ->numeric()
                    ->label('LTCG')
                    ->default(0),

                TextInput::make('stcg')
                    ->numeric()
                    ->label('STCG')
                    ->default(0),

                TextInput::make('speculative_income')
                    ->numeric()
                    ->label('Speculative Income')
                    ->default(0),

                TextInput::make('other_income')
                    ->numeric()
                    ->label('Other Income')
                    ->default(0),

                TextInput::make('dividend')
                    ->numeric()
                    ->label('Dividend')
                    ->default(0),

                TextInput::make('tax_paid')
                    ->numeric()
                    ->label('Tax paid')
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('financial_year')
                    ->label('FY'),

                TextColumn::make('salary')
                    ->label('Salary')
                    ->money(config('penny.currency'))
                    ->summarize(Sum::make()->money(config('penny.currency'))),

                TextColumn::make('rent')
                    ->label('Rent')
                    ->money(config('penny.currency'))
                    ->summarize(Sum::make()->money(config('penny.currency'))),

                TextColumn::make('stcg')
                    ->label('STCG')
                    ->money(config('penny.currency'))
                    ->summarize(Sum::make()->money(config('penny.currency'))),
                TextColumn::make('ltcg')
                    ->label('LTCG')
                    ->money(config('penny.currency'))
                    ->summarize(Sum::make()->money(config('penny.currency'))),
                TextColumn::make('dividend')
                    ->label('Dividend')
                    ->money(config('penny.currency'))
                    ->summarize(Sum::make()->money(config('penny.currency'))),
                TextColumn::make('speculative_income')
                    ->label('Speculative Income')
                    ->money(config('penny.currency'))
                    ->summarize(Sum::make()->money(config('penny.currency'))),
                TextColumn::make('other_income')
                    ->label('Other Income')
                    ->money(config('penny.currency'))
                    ->summarize(Sum::make()->money(config('penny.currency'))),
                TextColumn::make('tax_paid')
                    ->label('Tax Paid')
                    ->prefix('-')
                    ->money(config('penny.currency'))
                    ->summarize(Sum::make()->money(config('penny.currency'))),
            ])
            ->filters([
                SelectFilter::make('financial_year')
                    ->name('financial_year')
                    ->options(function () {
                        $array = ['2018', '2019', '2020', '2021', '2022'];

                        return array_combine($array, $array);
                    })
                    ->multiple(),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnualStatements::route('/'),
            'create' => Pages\CreateAnnualStatement::route('/create'),
            'edit' => Pages\EditAnnualStatement::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name'];
    }
}
