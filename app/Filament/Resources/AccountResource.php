<?php

namespace App\Filament\Resources;

use App\Enums\AccountType;
use App\Filament\Concerns\BulkDeleter;
use App\Filament\Concerns\UserFilterable;
use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use App\Services\AccountTransactionService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AccountResource extends Resource
{
    use BulkDeleter, UserFilterable;

    protected static ?string $model = Account::class;

    protected static ?string $slug = 'accounts';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?Account $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?Account $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                TextInput::make('name')
                    ->unique(ignorable: $form->getRecord())
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
                    ->disabledOn('edit')
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
                    ->money(config('penny.currency'))
                    ->summarize(
                        Sum::make('sum')
                            ->label('Total Available Balance')
                            ->money(config('penny.currency'))
                    ),

                TextColumn::make('initialBalance.balance')
                    ->label('Initial Balance - Date')
                    ->money(config('penny.currency'))
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
            ->actions([
                Action::make('transactions')
                    ->label('Transactions')
                    ->requiresConfirmation()
                    ->action(fn (AccountTransactionService $service, Account $record) => $service->sendTransactionsForUnaccountedPeriod($record, auth()->user())
                    ),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    self::deleteBulkAction(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
