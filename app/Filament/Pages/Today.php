<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Today extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.today';

    protected static ?string $navigationLabel = 'Today Transactions';

    protected static ?string $title = 'Today Transactions';
}
