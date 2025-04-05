<?php

namespace App\Livewire;

use App\Enums\PanelId;
use App\Models\Expense;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TodayExpenses extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Expense::query()
                    ->when(
                        PanelId::APP->isCurrentPanel(),
                        fn (Builder $q) => $q->where('user_id', auth()->id())
                    )
                    ->today()
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('account.name')->label('Account'),
                TextColumn::make('person.name')->label('Person'),
                TextColumn::make('description'),
                TextColumn::make('amount')
                    ->money(config('penny.currency'))
                    ->summarize(Sum::make('sum')->label('Total Amount')->money(config('penny.currency'))),

            ]);
    }
}
