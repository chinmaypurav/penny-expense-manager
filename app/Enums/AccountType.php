<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AccountType: string implements HasLabel
{
    case SAVINGS = 'savings';
    case CURRENT = 'current';
    case CREDIT = 'credit';
    case LOAN = 'loan';
    case TRADING = 'trading';
    case CASH = 'cash';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getShortCode(): string
    {
        return match ($this->value) {
            self::SAVINGS->value => 'SB',
            self::CURRENT->value => 'CC',
            self::CREDIT->value => 'CR',
            self::LOAN->value => 'LN',
            self::TRADING->value => 'TR',
            self::CASH->value => 'CA',
        };
    }
}
