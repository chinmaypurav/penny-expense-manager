<?php

namespace App\Filament\Resources;

use App\Enums\PanelId;
use App\Filament\Concerns\BulkDeleter;
use App\Filament\Concerns\UserFilterable;
use App\Filament\Resources\TransferResource\Pages;
use App\Models\Account;
use App\Models\Transfer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TransferResource extends Resource
{
    use BulkDeleter, UserFilterable;

    protected static ?string $model = Transfer::class;

    protected static ?string $slug = 'transfers';

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function form(Form $form): Form
    {
        $accounts = Account::query()
            ->get()
            ->keyBy('id')
            ->map(fn (Account $account) => $account->label);

        return $form
            ->schema([
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?Transfer $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?Transfer $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                Select::make('creditor_id')
                    ->label('Creditor account')
                    ->helperText('The account where money is going to')
                    ->options($accounts)
                    ->searchable()
                    ->different('debtor_id')
                    ->disabledOn('edit')
                    ->required(),

                Select::make('debtor_id')
                    ->label('Debtor account')
                    ->helperText('The account where money is coming from')
                    ->options($accounts)
                    ->searchable()
                    ->different('creditor_id')
                    ->disabledOn('edit')
                    ->required(),

                TextInput::make('description')
                    ->required(),

                DateTimePicker::make('transacted_at')
                    ->label('Transacted Date')
                    ->default(now())
                    ->beforeOrEqual(now())
                    ->required(),

                TextInput::make('amount')
                    ->required()
                    ->numeric(),

                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                self::getUserColumn(),

                TextColumn::make('creditor.name'),

                TextColumn::make('debtor.name'),

                TextColumn::make('description')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('transacted_at')
                    ->label('Transacted Date')
                    ->date()
                    ->dateTooltip('D')
                    ->sortable(),

                TextColumn::make('amount')
                    ->money(config('penny.currency'))
                    ->sortable(),
            ])
            ->filters([
                self::getUserFilter(),

                Filter::make('transacted_at')
                    ->form([
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
                SelectFilter::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
            ], FiltersLayout::AboveContentCollapsible)
            ->defaultSort('transacted_at', 'desc')
            ->actions([
                ReplicateAction::make()
                    ->visible(PanelId::APP->isCurrentPanel())
                    ->formData([
                        'transacted_at' => now(),
                    ]),
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
            'index' => Pages\ListTransfers::route('/'),
            'create' => Pages\CreateTransfer::route('/create'),
            'edit' => Pages\EditTransfer::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->when(PanelId::APP->isCurrentPanel(), fn (Builder $q) => $q->where('user_id', auth()->id()))
            ->with(['user', 'creditor', 'debtor']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'creditor.name', 'debtor.name', 'description'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->user && PanelId::FAMILY->isCurrentPanel()) {
            $details['User'] = $record->user->name;
        }

        if ($record->creditor) {
            $details['Creditor'] = $record->creditor->name;
        }

        if ($record->debtor) {
            $details['Debtor'] = $record->debtor->name;
        }

        $details['Description'] = $record->description;

        return $details;
    }
}
