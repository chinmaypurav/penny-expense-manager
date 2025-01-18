<?php

namespace App\Providers\Filament;

use App\Enums\PanelId;

class FamilyPanelProvider extends AppPanelProvider
{
    protected function getAppId(): string
    {
        return PanelId::FAMILY->value;
    }
}
