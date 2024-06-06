<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\IncomeExpenseResourceTrait;
use App\Filament\Resources\IncomeResource\Pages;
use App\Models\Income;
use Filament\Resources\Resource;

class IncomeResource extends Resource
{
    use IncomeExpenseResourceTrait;

    protected static ?string $model = Income::class;

    protected static ?string $slug = 'incomes';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomes::route('/'),
            'create' => Pages\CreateIncome::route('/create'),
            'edit' => Pages\EditIncome::route('/{record}/edit'),
        ];
    }
}
