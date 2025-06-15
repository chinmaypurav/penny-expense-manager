<?php

namespace App\Livewire;

use App\Enums\PanelId;
use App\Models\Expense;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class DayWiseExpenses extends BaseWidget
{
    use InteractsWithPageFilters;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Expense::query()
                    ->when(
                        PanelId::APP->isCurrentPanel(),
                        fn (Builder $q) => $q->where('user_id', auth()->id())
                    )
                    ->when(
                        Arr::get($this->pageFilters, 'transacted_at'),
                        fn (Builder $q, string $transactedAt) => $q->whereDate('transacted_at', $transactedAt),
                        fn (Builder $q) => $q->whereDate('transacted_at', today())
                    )
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('account.name')->label('Account'),
                TextColumn::make('person.name')->label('Person'),
                TextColumn::make('description'),
                TextColumn::make('amount')
                    ->money(config('coinager.currency'))
                    ->summarize(Sum::make('sum')->label('Total Amount')->money(config('coinager.currency'))),

            ]);
    }
}
