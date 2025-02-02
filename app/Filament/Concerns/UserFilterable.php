<?php

namespace App\Filament\Concerns;

use App\Enums\PanelId;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

trait UserFilterable
{
    public static function getUserFilterForm(): Select
    {
        return Select::make('user_id')
            ->label('Users')
            ->options(User::pluck('name', 'id'))
            ->multiple()
            ->visible(PanelId::FAMILY->isCurrentPanel());
    }

    public static function getUserFilter(): SelectFilter
    {
        return SelectFilter::make('user')
            ->relationship('user', 'name')
            ->preload()
            ->multiple()
            ->visible(PanelId::FAMILY->isCurrentPanel());
    }

    public static function getUserColumn(): TextColumn
    {
        return TextColumn::make('user.name')
            ->sortable()
            ->visible(PanelId::FAMILY->isCurrentPanel());
    }

    public function filterTableQuery(Builder $query): Builder
    {
        return parent::filterTableQuery($query)
            ->when(
                PanelId::APP->isCurrentPanel(),
                fn (Builder $q) => $q->where('user_id', auth()->id())
            );
    }
}
