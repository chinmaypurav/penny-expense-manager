<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BalanceResource\BalanceTable;
use App\Filament\Resources\BalanceResource\Pages\ListBalances;
use App\Models\Balance;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class BalanceResource extends Resource
{
    protected static ?string $model = Balance::class;

    protected static ?string $slug = 'balances';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    public static function table(Table $table): Table
    {
        return BalanceTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBalances::route('/'),
        ];
    }
}
