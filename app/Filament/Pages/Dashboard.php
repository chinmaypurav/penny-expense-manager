<?php

namespace App\Filament\Pages;

use App\Enums\PanelId;
use App\Filament\Concerns\UserFilterable;
use App\Filament\Widgets;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm, UserFilterable;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
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
            Widgets\IncomeChart::class,
            Widgets\ExpenseChart::class,
        ];
    }
}
