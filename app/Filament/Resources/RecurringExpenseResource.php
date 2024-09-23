<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RecurringIncomeRecurringExpenseTrait;
use App\Filament\Resources\RecurringExpenseResource\Pages;
use App\Models\RecurringExpense;
use Filament\Resources\Resource;

class RecurringExpenseResource extends Resource
{
    use RecurringIncomeRecurringExpenseTrait;

    protected static ?string $model = RecurringExpense::class;

    protected static ?string $slug = 'recurring-expenses';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecurringExpenses::route('/'),
            'create' => Pages\CreateRecurringExpense::route('/create'),
            'edit' => Pages\EditRecurringExpense::route('/{record}/edit'),
        ];
    }
}
