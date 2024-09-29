<?php

namespace App\Enums;

use App\Concerns\Enumerable;
use Filament\Support\Contracts\HasLabel;

enum AccountType: string implements HasLabel
{
    use Enumerable;

    case SAVINGS = 'savings';
    case CURRENT = 'current';
    case CREDIT = 'credit';
    case TRADING = 'trading';
    case CASH = 'cash';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
