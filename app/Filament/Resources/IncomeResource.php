<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\IncomeExpenseResourceTrait;
use App\Filament\Resources\IncomeResource\Pages\CreateIncome;
use App\Filament\Resources\IncomeResource\Pages\EditIncome;
use App\Filament\Resources\IncomeResource\Pages\ListIncomes;
use App\Models\Income;
use Filament\Resources\Resource;

class IncomeResource extends Resource
{
    use IncomeExpenseResourceTrait;

    protected static ?string $model = Income::class;

    protected static ?string $slug = 'incomes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';

    public static function getPages(): array
    {
        return [
            'index' => ListIncomes::route('/'),
            'create' => CreateIncome::route('/create'),
            'edit' => EditIncome::route('/{record}/edit'),
        ];
    }
}
