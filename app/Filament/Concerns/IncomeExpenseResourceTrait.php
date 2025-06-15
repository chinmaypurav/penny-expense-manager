<?php

namespace App\Filament\Concerns;

use App\Enums\PanelId;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use App\Services\TableFilterService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin Income|Expense
 */
trait IncomeExpenseResourceTrait
{
    use BulkDeleter, UserFilterable;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (Expense|Income|null $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (Expense|Income|null $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                Select::make('person_id')
                    ->relationship(
                        'person',
                        'name'
                    )
                    ->nullable()
                    ->preload()
                    ->searchable(),

                Select::make('account_id')
                    ->relationship(
                        'account',
                        'name',
                        fn (Builder $query): Builder => $query->where('user_id', auth()->id())
                    )
                    ->getOptionLabelFromRecordUsing(fn (Account $record): string => $record->label)
                    ->required()
                    ->preload()
                    ->searchable(),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->preload()
                    ->searchable(),

                TextInput::make('description')
                    ->required(),

                DateTimePicker::make('transacted_at')
                    ->label('Transacted Date')
                    ->default(now())
                    ->maxDate(now())
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

                TextColumn::make('person.name'),

                TextColumn::make('account.name'),

                TextColumn::make('category.name'),

                TextColumn::make('description')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('transacted_at')
                    ->label('Transacted Date')
                    ->date()
                    ->dateTooltip('D')
                    ->sortable(),

                TextColumn::make('amount')
                    ->money(config('coinager.currency'))
                    ->alignRight()
                    ->sortable(),
            ])
            ->filters([
                self::getUserFilter(),

                Filter::make('transacted_at')
                    ->schema([
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
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(TableFilterService::getCategoryFilter())
                    ->preload()
                    ->searchable(),

                SelectFilter::make('account_id')
                    ->label('Account')
                    ->options(TableFilterService::getAccountFilter(Auth::id()))
                    ->preload()
                    ->searchable(),

                SelectFilter::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
            ], FiltersLayout::AboveContentCollapsible)
            ->defaultSort('transacted_at', 'desc')
            ->recordActions([
                ReplicateAction::make()
                    ->visible(PanelId::APP->isCurrentPanel())
                    ->beforeReplicaSaved(function (Expense|Income $replica) {
                        $replica->setAttribute('transacted_at', now());
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    self::deleteBulkAction(),
                ]),
            ]);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->when(PanelId::APP->isCurrentPanel(), fn (Builder $q) => $q->where('user_id', auth()->id()))
            ->with(['user', 'person', 'account']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'person.name', 'account.name', 'description'];
    }

    public static function getGlobalSearchResultDetails(Model|Income|Expense $record): array
    {
        if ($record->user && PanelId::FAMILY->isCurrentPanel()) {
            $details['User'] = $record->user->name;
        }

        if ($record->person) {
            $details['Person'] = $record->person->name;
        }

        if ($record->account) {
            $details['Account'] = $record->account->name;
        }

        $details['Description'] = $record->description;

        return $details;
    }
}
