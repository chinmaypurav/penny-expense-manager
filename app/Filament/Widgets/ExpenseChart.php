<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\CashFlowChartTrait;
use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class ExpenseChart extends ChartWidget
{
    use CashFlowChartTrait, InteractsWithPageFilters;

    protected static ?string $heading = 'Expenses';

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Expenses',
                    'data' => $this->processMissingDates($this->getExpenseData()),
                    'backgroundColor' => '#ef4444',
                    'borderColor' => '#ef4444',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getExpenseData(): array
    {
        $startDate = Arr::get($this->filters, 'start_date');
        $endDate = Arr::get($this->filters, 'end_date');

        return Expense::query()
            ->selectRaw('DATE(transacted_at) as day, SUM(amount) as amount')
            ->where('user_id', auth()->id())
            ->when($startDate && $endDate,
                fn (Builder $q) => $q->whereDate('transacted_at', '>=', $startDate)
                    ->whereDate('transacted_at', '<=', $endDate),
                fn (Builder $q) => $q->whereDate('transacted_at', '>=', now()->startOfMonth())
                    ->whereDate('transacted_at', '<=', now()->endOfMonth())
            )
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('amount', 'day')
            ->toArray();
    }
}
