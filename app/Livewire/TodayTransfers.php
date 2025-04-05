<?php

namespace App\Livewire;

use App\Enums\PanelId;
use App\Models\Transfer;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TodayTransfers extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transfer::query()
                    ->when(
                        PanelId::APP->isCurrentPanel(),
                        fn (Builder $q) => $q->where('user_id', auth()->id())
                    )
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('creditor.name')->label('Creditor'),
                TextColumn::make('debtor.name')->label('Debtor'),
                TextColumn::make('description'),
                TextColumn::make('amount')
                    ->money(config('penny.currency'))
                    ->summarize(Sum::make('sum')->label('Total Amount')->money(config('penny.currency'))),
            ]);
    }
}
