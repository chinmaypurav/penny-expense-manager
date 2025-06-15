<?php

namespace App\Filament\Pages;

use App\Enums\PanelId;
use App\Filament\Concerns\UserFilterable;
use App\Filament\Widgets\ExpenseChart;
use App\Filament\Widgets\IncomeChart;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm, UserFilterable;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        self::getUserFilterForm(),
                        DatePicker::make('start_date')->default(now()->startOfMonth()),
                        DatePicker::make('end_date')->default(now()->endOfMonth()),
                    ])
                    ->columns(PanelId::FAMILY->isCurrentPanel() ? 3 : 2),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            IncomeChart::class,
            ExpenseChart::class,
        ];
    }
}
