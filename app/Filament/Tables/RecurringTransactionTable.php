<?php

namespace App\Filament\Tables;

use App\Enums\Frequency;
use App\Filament\Concerns\BulkDeleter;
use App\Filament\Concerns\UserFilterable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

abstract class RecurringTransactionTable
{
    use BulkDeleter, UserFilterable;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                self::getUserColumn(),

                TextColumn::make('person.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('account.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description'),

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
