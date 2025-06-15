<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RecurringIncomeRecurringExpenseTrait;
use App\Filament\Resources\RecurringExpenseResource\Pages\CreateRecurringExpense;
use App\Filament\Resources\RecurringExpenseResource\Pages\EditRecurringExpense;
use App\Filament\Resources\RecurringExpenseResource\Pages\ListRecurringExpenses;
use App\Models\RecurringExpense;
use Filament\Resources\Resource;

class RecurringExpenseResource extends Resource
{
    use RecurringIncomeRecurringExpenseTrait;

    protected static ?string $model = RecurringExpense::class;

    protected static ?string $slug = 'recurring-expenses';

    protected static string|\UnitEnum|null $navigationGroup = 'Recurring Transactions';

    public static function getPages(): array
    {
        return [
            'index' => ListRecurringExpenses::route('/'),
            'create' => CreateRecurringExpense::route('/create'),
            'edit' => EditRecurringExpense::route('/{record}/edit'),
        ];
    }
}
