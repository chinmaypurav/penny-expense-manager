<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;

class Today extends Page implements HasForms
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.today';

    protected static ?string $navigationLabel = 'Today Transactions';

    protected static ?string $title = 'Today Transactions';

    public function mount(): void
    {
        $this->form->fill([
            'transacted_at' => today(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            DatePicker::make('transacted_at')->live(),
        ])->statePath('filters');
    }
}
