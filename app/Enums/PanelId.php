<?php

namespace App\Enums;

use Filament\Facades\Filament;
use Filament\Panel;

enum PanelId: string
{
    case APP = 'app';
    case FAMILY = 'family';

    public function getId(): string
    {
        return match ($this) {
            self::APP => 'app',
            self::FAMILY => 'family',
        };
    }

    public function getPath(): string
    {
        return match ($this) {
            self::APP => 'app',
            self::FAMILY => 'family',
        };
    }

    public function getHomeUrl(): string
    {
        return match ($this) {
            self::APP => url('app'),
            self::FAMILY => url('family'),
        };
    }

    public function getSwitchButtonLabel(): string
    {
        return match ($this) {
            self::APP => 'Individual view',
            self::FAMILY => 'Family view',
        };
    }

    public function getSwitchButtonIcon(): string
    {
        return match ($this) {
            self::APP => 'heroicon-o-user-circle',
            self::FAMILY => 'heroicon-o-user-group',
        };
    }

    public function getPanel(): Panel
    {
        return Filament::getPanel($this->value);
    }

    public function getMenuItemActionName(): string
    {
        return match ($this) {
            self::APP => 'app-menu-item-action',
            self::FAMILY => 'family-menu-item-action',
        };
    }

    public function setCurrentPanel(): void
    {
        Filament::setCurrentPanel($this->getPanel());
    }

    public function isCurrentPanel(): bool
    {
        return Filament::getId() === $this->value;
    }
}
