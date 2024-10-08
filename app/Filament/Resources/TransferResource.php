<?php

namespace App\Filament\Resources;

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
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TransferResource extends Resource
{
    protected static ?string $model = Transfer::class;

    protected static ?string $slug = 'transfers';

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function form(Form $form): Form
    {
        $accounts = Account::query()
            ->where('user_id', auth()->id())
            ->pluck('name', 'id');

        return $form
            ->schema([
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?Transfer $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?Transfer $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                Select::make('creditor_id')
                    ->options($accounts)
                    ->different('debtor_id')
                    ->required(),

                Select::make('debtor_id')
                    ->options($accounts)
                    ->different('creditor_id')
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
                TextColumn::make('creditor.name'),

                TextColumn::make('debtor.name'),

                TextColumn::make('description')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('transacted_at')
                    ->label('Transacted Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('amount')
                    ->money(config('penny.currency'))
                    ->sortable(),
            ])
            ->filters([
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
            ->defaultSort('transacted_at')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'creditor', 'debtor']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'creditor.name', 'debtor.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->user) {
            $details['User'] = $record->user->name;
        }

        if ($record->creditor) {
            $details['Creditor'] = $record->creditor->name;
        }

        if ($record->debtor) {
            $details['Debtor'] = $record->debtor->name;
        }

        return $details;
    }
}
