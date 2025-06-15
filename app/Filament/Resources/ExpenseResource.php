<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\IncomeExpenseResourceTrait;
use App\Filament\Resources\ExpenseResource\Pages\CreateExpense;
use App\Filament\Resources\ExpenseResource\Pages\EditExpense;
use App\Filament\Resources\ExpenseResource\Pages\ListExpenses;
use App\Models\Expense;
use Filament\Resources\Resource;

class ExpenseResource extends Resource
{
    use IncomeExpenseResourceTrait;

    protected static ?string $model = Expense::class;

    protected static ?string $slug = 'expenses';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-down';

    public static function getPages(): array
    {
        return [
            'index' => ListExpenses::route('/'),
            'create' => CreateExpense::route('/create'),
            'edit' => EditExpense::route('/{record}/edit'),
        ];
    }
}
