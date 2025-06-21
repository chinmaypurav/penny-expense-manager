<?php

namespace App\Filament\Resources\AccountResource;

use App\Enums\AccountType;
use App\Filament\Concerns\BulkDeleter;
use App\Filament\Concerns\UserFilterable;
use App\Models\Account;
use App\Services\AccountTransactionService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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
                Action::make('transactions')
                    ->label('Transactions')
                    ->requiresConfirmation()
                    ->action(fn (AccountTransactionService $service, Account $record) => $service->sendProvisionalTransactions($record, auth()->user())
                    ),
                Action::make('transactions-view')
                    ->label('Transactions View')
                    ->color(Color::Fuchsia)
                    ->url(fn (Account $record) => route('accounts.transactions', [
                        'account' => $record->id,
                    ])
                    ),
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
