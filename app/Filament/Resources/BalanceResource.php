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
                    ->money(config('penny.currency')),

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
                    ->relationship(
                        'account',
                        'name',
                        fn (Builder $query): Builder => $query->where('user_id', auth()->id())
                    ),

                SelectFilter::make('record_type')
                    ->options(RecordType::class),
            ], layout: FiltersLayout::AboveContent);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBalances::route('/'),
        ];
    }
}
