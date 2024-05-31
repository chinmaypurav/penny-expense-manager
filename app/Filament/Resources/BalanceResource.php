<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BalanceResource\Pages;
use App\Models\Balance;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BalanceResource extends Resource
{
    protected static ?string $model = Balance::class;

    protected static ?string $slug = 'balances';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount'),

                TextColumn::make('recorded_until')
                    ->date(),

                ToggleColumn::make('is_initial_record'),

                TextColumn::make('record_type'),
            ])
            ->filters([
                //
            ]);
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
