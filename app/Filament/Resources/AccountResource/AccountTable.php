<?php

namespace App\Filament\Resources\AccountResource;

use App\Enums\AccountType;
use App\Filament\Concerns\BulkDeleter;
use App\Filament\Concerns\UserFilterable;
use App\Filament\Resources\ExpenseResource;
use App\Filament\Resources\IncomeResource;
use App\Models\Account;
use Filament\Actions\Action;
use Filament\Schemas\Components\Icon;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AccountTable
{
    use BulkDeleter, UserFilterable;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                static::getUserColumn(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('account_type'),

                TextColumn::make('current_balance')
                    ->money(config('coinager.currency'))
                    ->summarize(
                        Sum::make('sum')
                            ->label('Total Available Balance')
                            ->money(config('coinager.currency'))
                    ),

                TextColumn::make('initial_balance')
                    ->label('Initial Balance - Date')
                    ->money(config('coinager.currency'))
                    ->description(fn (Account $record) => $record->initial_date->toDateString()),
            ])
            ->filters([
                self::getUserFilter(),
                SelectFilter::make('account_type')
                    ->multiple()
                    ->options(
                        collect(AccountType::cases())
                            ->keyBy(fn (AccountType $type) => $type->value)
                            ->map(fn (AccountType $type) => $type->getLabel())
                            ->toArray(),
                    ),
            ])
            ->paginated(false)
            ->recordActions([
                Action::make('create-incomes')
                    ->label('Income')
                    ->url(fn (Account $record) => IncomeResource::getUrl('create', [
                        'account_id' => $record->id,
                    ]))
                    ->button()
                    ->tooltip('Create Income')
                    ->icon(
                        fn () => Icon::make('create-income-icon')->icon('heroicon-o-arrow-trending-up')->color(Color::Green)
                    ),
                Action::make('create-expense')
                    ->label(fn (Account $record) => 'Expense')
                    ->color(Color::Red)
                    ->url(fn (Account $record) => ExpenseResource::getUrl('create', [
                        'account_id' => $record->id,
                    ]))
                    ->tooltip('Create Expense')
                    ->button()
                    ->icon(
                        fn () => Icon::make('create-expense-icon')->icon('heroicon-o-arrow-trending-down')->color(Color::Red)
                    ),
                Action::make('transactions-view')
                    ->label('Transactions')
                    ->button()
                    ->color(Color::Fuchsia)
                    ->url(fn (Account $record) => route('accounts.transactions', [
                        'account' => $record->id,
                    ])
                    ),
            ]);
    }
}
