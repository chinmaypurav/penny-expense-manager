<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\IncomeExpenseResourceTrait;
use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Resources\Resource;

class ExpenseResource extends Resource
{
    use IncomeExpenseResourceTrait;

    protected static ?string $model = Expense::class;

    protected static ?string $slug = 'expenses';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
