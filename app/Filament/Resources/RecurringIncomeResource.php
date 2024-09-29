<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RecurringIncomeRecurringExpenseTrait;
use App\Filament\Resources\RecurringIncomeResource\Pages;
use App\Models\RecurringIncome;
use Filament\Resources\Resource;

class RecurringIncomeResource extends Resource
{
    use RecurringIncomeRecurringExpenseTrait;

    protected static ?string $model = RecurringIncome::class;

    protected static ?string $slug = 'recurring-incomes';

    protected static ?string $navigationGroup = 'Recurring Transactions';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecurringIncomes::route('/'),
            'create' => Pages\CreateRecurringIncome::route('/create'),
            'edit' => Pages\EditRecurringIncome::route('/{record}/edit'),
        ];
    }
}
