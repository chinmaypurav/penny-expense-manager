<?php

namespace App\Providers\Filament;

use App\Enums\PanelId;
use App\Filament\Resources\AccountResource;
use App\Filament\Resources\ExpenseResource;
use App\Filament\Resources\IncomeResource;
use App\Filament\Resources\RecurringExpenseResource;
use App\Filament\Resources\RecurringIncomeResource;
use App\Filament\Resources\RecurringTransferResource;
use App\Filament\Resources\TransferResource;
use App\Filament\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Panel;
use Filament\Support\Colors\Color;

class FamilyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return parent::panel($panel)
            ->path(PanelId::FAMILY->getPath())
            ->resources([
                UserResource::class,

                AccountResource::class,
                IncomeResource::class,
                ExpenseResource::class,
                TransferResource::class,

                RecurringIncomeResource::class,
                RecurringExpenseResource::class,
                RecurringTransferResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->colors([
                'primary' => Color::Green,
            ])->userMenuItems([
                Action::make(PanelId::APP->getMenuItemActionName())
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
