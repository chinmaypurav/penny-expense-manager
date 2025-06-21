<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecurringCashFlowOverviewWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class RecurringCashFlowOverviewDashboard extends BaseDashboard
{
    protected static string|\UnitEnum|null $navigationGroup = 'Recurring Transactions';

    protected static string $routePath = 'recurring-overview';

    protected static ?string $title = 'Recurring CashFlow Overview';

    public function getWidgets(): array
    {
        return [
            RecurringCashFlowOverviewWidget::class,
        ];
    }
}
