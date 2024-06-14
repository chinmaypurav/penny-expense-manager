<?php

namespace App\Enums;

use App\Concerns\Enumerable;

enum AccountType: string
{
    use Enumerable;

    case SAVINGS = 'savings';
    case CURRENT = 'current';
    case CREDIT = 'credit';
    case TRADING = 'trading';
    case CASH = 'cash';
}
