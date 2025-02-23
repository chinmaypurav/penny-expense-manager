<?php

namespace App\Providers\Filament;

use App\Enums\PanelId;
use App\Filament\Resources;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\Support\Colors\Color;

class FamilyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return parent::panel($panel)
            ->path(PanelId::FAMILY->getPath())
            ->resources([
                Resources\UserResource::class,

                Resources\AccountResource::class,
                Resources\IncomeResource::class,
                Resources\ExpenseResource::class,
                Resources\TransferResource::class,

                Resources\RecurringIncomeResource::class,
                Resources\RecurringExpenseResource::class,
                Resources\RecurringTransferResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->colors([
                'primary' => Color::Green,
            ])->userMenuItems([
                MenuItem::make()
                    ->label(PanelId::APP->getSwitchButtonLabel())
                    ->url(fn (): string => PanelId::APP->getHomeUrl())
                    ->icon(PanelId::APP->getSwitchButtonIcon()),
            ]);
    }

    protected function getPanelId(): string
    {
        return PanelId::FAMILY->getId();
    }
}
