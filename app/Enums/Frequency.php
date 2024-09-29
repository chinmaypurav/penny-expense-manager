<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Frequency: string implements HasLabel
{
    case ONCE = 'once';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
