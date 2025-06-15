<?php

use App\Enums\PanelId;
use Filament\Actions\Action;

it('displays family switch panel button', function () {
    $panel = PanelId::APP->getPanel();

    $menuItems = collect($panel->getUserMenuItems());

    $labels = $menuItems->map(fn (Action $action) => $action->getLabel())->toArray();
    $urls = $menuItems->map(fn (Action $action) => $action->getUrl())->toArray();

    $this->assertContains(PanelId::FAMILY->getSwitchButtonLabel(), $labels);
    $this->assertContains(PanelId::FAMILY->getHomeUrl(), $urls);
});

it('displays individual switch panel button', function () {
    $panel = PanelId::FAMILY->getPanel();

    $menuItems = collect($panel->getUserMenuItems());

    $labels = $menuItems->map(fn (Action $action) => $action->getLabel())->toArray();
    $urls = $menuItems->map(fn (Action $action) => $action->getUrl())->toArray();

    $this->assertContains(PanelId::APP->getSwitchButtonLabel(), $labels);
    $this->assertContains(PanelId::APP->getHomeUrl(), $urls);
});
