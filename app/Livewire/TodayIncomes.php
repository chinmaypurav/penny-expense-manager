<?php

namespace App\Livewire;

use App\Enums\PanelId;
use App\Models\Income;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TodayIncomes extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Income::query()
                    ->when(
                        PanelId::APP->isCurrentPanel(),
                        fn (Builder $q) => $q->where('user_id', auth()->id())
                    )
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
