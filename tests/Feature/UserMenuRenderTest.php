<?php

use App\Enums\PanelId;
use Filament\Navigation\MenuItem;

it('displays family switch panel button', function () {
    $panel = PanelId::APP->getPanel();

    $menuItems = collect($panel->getUserMenuItems());

    $labels = $menuItems->map(fn (MenuItem $item) => $item->getLabel())->toArray();
    $urls = $menuItems->map(fn (MenuItem $item) => $item->getUrl())->toArray();

    $this->assertContains(PanelId::FAMILY->getSwitchButtonLabel(), $labels);
    $this->assertContains(PanelId::FAMILY->getHomeUrl(), $urls);
});

it('displays individual switch panel button', function () {
    $panel = PanelId::FAMILY->getPanel();

    $menuItems = collect($panel->getUserMenuItems());

    $labels = $menuItems->map(fn (MenuItem $item) => $item->getLabel())->toArray();
    $urls = $menuItems->map(fn (MenuItem $item) => $item->getUrl())->toArray();

    $this->assertContains(PanelId::APP->getSwitchButtonLabel(), $labels);
    $this->assertContains(PanelId::APP->getHomeUrl(), $urls);
});
