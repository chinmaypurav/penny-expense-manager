<?php

namespace App\Enums;

enum AccountType: string
{
    case SAVINGS = 'savings';
    case CURRENT = 'current';
    case CREDIT = 'credit';
    case TRADING = 'trading';
    case CASH = 'cash';

    public static function all(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}
