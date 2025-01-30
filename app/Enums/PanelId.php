<?php

namespace App\Enums;

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
            self::APP => 'app',
            self::FAMILY => 'family',
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
}
