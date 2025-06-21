<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecurringExpenseResource\Pages\CreateRecurringExpense;
use App\Filament\Resources\RecurringExpenseResource\Pages\EditRecurringExpense;
use App\Filament\Resources\RecurringExpenseResource\Pages\ListRecurringExpenses;
use App\Filament\Resources\RecurringExpenseResource\RecurringExpenseForm;
use App\Filament\Resources\RecurringExpenseResource\RecurringExpenseTable;
use App\Models\RecurringExpense;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class RecurringExpenseResource extends Resource
{
    protected static ?string $model = RecurringExpense::class;

    protected static ?string $slug = 'recurring-expenses';

    protected static string|\UnitEnum|null $navigationGroup = 'Recurring Transactions';

    public static function form(Schema $schema): Schema
    {
        return RecurringExpenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecurringExpenseTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecurringExpenses::route('/'),
            'create' => CreateRecurringExpense::route('/create'),
            'edit' => EditRecurringExpense::route('/{record}/edit'),
        ];
    }
}
