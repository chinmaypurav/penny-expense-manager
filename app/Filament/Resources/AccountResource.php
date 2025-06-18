<?php

namespace App\Filament\Resources;

use App\Enums\AccountType;
use App\Filament\Concerns\BulkDeleter;
use App\Filament\Concerns\UserFilterable;
use App\Filament\Resources\AccountResource\AccountForm;
use App\Filament\Resources\AccountResource\Pages\CreateAccount;
use App\Filament\Resources\AccountResource\Pages\EditAccount;
use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Models\Account;
use App\Services\AccountTransactionService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AccountResource extends Resource
{
    use BulkDeleter, UserFilterable;

    protected static ?string $model = Account::class;

    protected static ?string $slug = 'accounts';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Schema $schema): Schema
    {
        return AccountForm::configure($schema);
    }

    public static function table(Table $table): Table
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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    self::deleteBulkAction(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccounts::route('/'),
            'create' => CreateAccount::route('/create'),
            'edit' => EditAccount::route('/{record}/edit'),
        ];
    }
}
