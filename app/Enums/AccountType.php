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
}
