<?php

namespace App\Filament\Resources\RecurringTransferResource;

use App\Enums\Frequency;
use App\Filament\Concerns\BulkDeleter;
use App\Filament\Concerns\UserFilterable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RecurringTransferTable
{
    use BulkDeleter, UserFilterable;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                self::getUserColumn(),

                TextColumn::make('creditor.name'),

                TextColumn::make('debtor.name'),

                TextColumn::make('description')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('next_transaction_at')
                    ->label('Next Transaction Date')
                    ->date(),

                TextColumn::make('frequency'),

                TextColumn::make('remaining_recurrences'),
            ])
            ->filters([
                self::getUserFilter(),

                SelectFilter::make('frequency')
                    ->options(Frequency::class),
            ])
            ->defaultSort('next_transaction_at')
            ->recordActions([
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
