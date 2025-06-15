<?php

namespace App\Providers\Filament;

use App\Enums\PanelId;
use Filament\Actions\Action;
use Filament\Panel;
use Filament\Support\Colors\Color;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return parent::panel($panel)
            ->default()
            ->path(PanelId::APP->getPath())
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->colors([
                'primary' => Color::Emerald,
            ])->userMenuItems([
                Action::make(PanelId::FAMILY->getMenuItemActionName())
                    ->label(PanelId::FAMILY->getSwitchButtonLabel())
                    ->url(fn (): string => PanelId::FAMILY->getHomeUrl())
                    ->icon(PanelId::FAMILY->getSwitchButtonIcon()),
            ]);
    }

    protected function getPanelId(): string
    {
        return PanelId::APP->getId();
    }
}
