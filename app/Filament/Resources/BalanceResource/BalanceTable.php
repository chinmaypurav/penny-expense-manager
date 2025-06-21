<?php

namespace App\Filament\Resources\BalanceResource;

use App\Enums\RecordType;
use App\Models\Balance;
use App\Services\AccountTransactionService;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BalanceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('balance')
                    ->money(config('coinager.currency')),

                TextColumn::make('recorded_until')
                    ->date(),

                TextColumn::make('record_type'),
            ])
            ->recordActions([
                Action::make('transactions')
                    ->label('Transactions')
                    ->requiresConfirmation()
                    ->action(fn (AccountTransactionService $service, Balance $record) => $service->sendTransactionsForBalancePeriod($record, auth()->user())
                    ),
            ])
            ->defaultSort('recorded_until', 'desc')
            ->filters([
                SelectFilter::make('account_id')
                    ->label('Account')
                    ->relationship(
                        'account',
                        'name',
                        fn (Builder $query): Builder => $query->where('user_id', auth()->id())
                    ),

                SelectFilter::make('record_type')
                    ->options(RecordType::class),
            ], layout: FiltersLayout::AboveContent);
    }
}
