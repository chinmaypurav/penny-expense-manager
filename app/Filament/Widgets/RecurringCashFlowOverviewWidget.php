<?php

namespace App\Filament\Widgets;

use App\Services\RecurringCashFlowService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RecurringCashFlowOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $startDate = today();
        $endDate = today()->addYear();

        $totalIncomes = RecurringCashFlowService::processRecurringIncomes(
            auth()->user(),
            $startDate,
            $endDate
        );

        $totalExpenses = RecurringCashFlowService::processRecurringExpenses(
            auth()->user(),
            $startDate,
            $endDate
        );

        $disposableIncome = $totalIncomes - $totalExpenses;

        $symbol = config('coinager.currency_symbol');

        return [
            Stat::make('Total Estimated Incomes', $symbol.' '.number_format($totalIncomes)),
            Stat::make('Total Estimated Expenses', $symbol.' '.number_format($totalExpenses)),
            Stat::make('Estimated Disposable Income', $symbol.' '.number_format($disposableIncome)),
        ];
    }
}
