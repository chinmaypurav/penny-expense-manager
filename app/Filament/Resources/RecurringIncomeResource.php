<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RecurringIncomeRecurringExpenseTrait;
use App\Filament\Resources\RecurringIncomeResource\Pages\CreateRecurringIncome;
use App\Filament\Resources\RecurringIncomeResource\Pages\EditRecurringIncome;
use App\Filament\Resources\RecurringIncomeResource\Pages\ListRecurringIncomes;
use App\Models\RecurringIncome;
use Filament\Resources\Resource;

class RecurringIncomeResource extends Resource
{
    use RecurringIncomeRecurringExpenseTrait;

    protected static ?string $model = RecurringIncome::class;

    protected static ?string $slug = 'recurring-incomes';

    protected static string|\UnitEnum|null $navigationGroup = 'Recurring Transactions';

    public static function getPages(): array
    {
        return [
            'index' => ListRecurringIncomes::route('/'),
            'create' => CreateRecurringIncome::route('/create'),
            'edit' => EditRecurringIncome::route('/{record}/edit'),
        ];
    }
}
