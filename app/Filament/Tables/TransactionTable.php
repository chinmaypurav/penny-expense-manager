<?php

namespace App\Filament\Tables;

use App\Enums\PanelId;
use App\Filament\Concerns\BulkDeleter;
use App\Filament\Concerns\UserFilterable;
use App\Models\Expense;
use App\Models\Income;
use App\Services\TableFilterService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

abstract class TransactionTable
{
    use BulkDeleter, UserFilterable;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                self::getUserColumn(),

                TextColumn::make('person.name'),

                TextColumn::make('account.name'),

                TextColumn::make('category.name'),

                TextColumn::make('description')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('transacted_at')
                    ->label('Transacted Date')
                    ->date()
                    ->dateTooltip('D')
                    ->sortable(),

                TextColumn::make('amount')
                    ->money(config('coinager.currency'))
                    ->alignRight()
                    ->sortable(),
            ])
            ->filters([
                self::getUserFilter(),

                Filter::make('transacted_at')
                    ->schema([
                        DatePicker::make('transacted_from')->default(now()->startOfMonth()),
                        DatePicker::make('transacted_until')->default(now()->endOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['transacted_from'],
                                fn (Builder $query, $date): Builder => $query
                                    ->whereDate('transacted_at', '>=', $date),
                            )
                            ->when(
                                $data['transacted_until'],
                                fn (Builder $query, $date): Builder => $query
                                    ->whereDate('transacted_at', '<=', $date),
                            );
                    }),
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(TableFilterService::getCategoryFilter())
                    ->preload()
                    ->searchable(),

                SelectFilter::make('account_id')
                    ->label('Account')
                    ->options(TableFilterService::getAccountFilter(Auth::id()))
                    ->preload()
                    ->searchable(),

                SelectFilter::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
            ], FiltersLayout::AboveContentCollapsible)
            ->defaultSort('transacted_at', 'desc')
            ->recordActions([
                ReplicateAction::make()
                    ->visible(PanelId::APP->isCurrentPanel())
                    ->beforeReplicaSaved(function (Expense|Income $replica) {
                        $replica->setAttribute('transacted_at', now());
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    self::deleteBulkAction(),
                ]),
            ]);
    }
}
