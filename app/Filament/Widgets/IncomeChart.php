<?php

namespace App\Filament\Widgets;

use App\Enums\PanelId;
use App\Filament\Concerns\CashFlowChartTrait;
use App\Models\Income;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class IncomeChart extends ChartWidget
{
    use CashFlowChartTrait, InteractsWithPageFilters;

    protected static ?string $heading = 'Incomes';

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Incomes',
                    'data' => $this->processMissingDates($this->getIncomeData()),
                    'backgroundColor' => '#22c55e',
                    'borderColor' => '#22c55e',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getIncomeData(): array
    {
        $startDate = Arr::get($this->filters, 'start_date');
        $endDate = Arr::get($this->filters, 'end_date');
        $userIds = Arr::get($this->filters, 'users', []);

        return Income::query()
            ->selectRaw('DATE(transacted_at) as day, SUM(amount) as amount')
            ->when(
                PanelId::FAMILY->isCurrentPanel(),
                fn (Builder $q) => $q->when($userIds,
                    fn (Builder $q) => $q->whereIn('user_id', $userIds)),
                fn (Builder $q) => $q->where('user_id', auth()->id())
            )
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
