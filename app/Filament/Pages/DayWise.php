<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class DayWise extends Page implements HasForms
{
    use HasFiltersForm;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected string $view = 'filament.pages.day-wise';

    protected static ?string $navigationLabel = 'Day Wise Transactions';

    protected static ?string $title = 'Day Wise Transactions';

    public function mount(): void
    {
        $this->form->fill([
            'transacted_at' => today(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('transacted_at')->live(),
        ])->statePath('filters');
    }
}
