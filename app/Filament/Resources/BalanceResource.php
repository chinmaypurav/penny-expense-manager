<?php

namespace App\Filament\Resources;

use App\Enums\RecordType;
use App\Filament\Resources\BalanceResource\Pages;
use App\Models\Balance;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BalanceResource extends Resource
{
    protected static ?string $model = Balance::class;

    protected static ?string $slug = 'balances';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('balance')
                    ->money('INR'),

                TextColumn::make('recorded_until')
                    ->date(),

                IconColumn::make('is_initial_record')
                    ->label('Initial Record')
                    ->boolean(),

                TextColumn::make('record_type'),
            ])
            ->filters([
                SelectFilter::make('account_id')
                    ->label('Account')
                    ->relationship('account', 'name'),

                SelectFilter::make('record_type')
                    ->options(RecordType::all()),
            ], layout: FiltersLayout::AboveContent);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBalances::route('/'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['account']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['account.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->account) {
            $details['Account'] = $record->account->name;
        }

        return $details;
    }
}
