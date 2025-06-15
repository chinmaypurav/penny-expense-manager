<?php

namespace App\Filament\Resources;

use App\Enums\AccountType;
use App\Filament\Concerns\BulkDeleter;
use App\Filament\Concerns\UserFilterable;
use App\Filament\Resources\AccountResource\Pages\CreateAccount;
use App\Filament\Resources\AccountResource\Pages\EditAccount;
use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Models\Account;
use App\Services\AccountTransactionService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
        return $schema
            ->components([
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?Account $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?Account $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                TextInput::make('name')
                    ->unique(ignorable: $schema->getRecord())
                    ->required()
                    ->columnSpan(fn (string $operation): int => $operation === 'create' ? 1 : 2),

                Select::make('account_type')
                    ->disabledOn('edit')
                    ->required()
                    ->options(AccountType::class),

                TextInput::make('current_balance')
                    ->label(fn (string $operation): string => $operation === 'create'
                        ? 'Initial Balance'
                        : 'Current Balance'
                    )
                    ->required()
                    ->numeric(),

                DatePicker::make('initial_date')
                    ->label('Initial Balance Date')
                    ->default(today())
                    ->beforeOrEqual(today())
                    ->required(),

                TextInput::make('initialBalance.balance')
                    ->label('Initial Balance')
                    ->hiddenOn(['create'])
                    ->formatStateUsing(fn (?Account $record) => $record?->initialBalance?->balance)
                    ->readOnly(),
            ]);
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

                TextColumn::make('initialBalance.balance')
                    ->label('Initial Balance - Date')
                    ->money(config('coinager.currency'))
                    ->description(fn (Account $record) => $record->initialBalance?->recorded_until?->toDateString()),
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
