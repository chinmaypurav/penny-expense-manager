<?php

namespace App\Enums;

enum AccountType: string
{
    case SAVINGS = 'Savings';
    case CURRENT = 'Current';
    case CREDIT = 'Credit';
    case TRADING = 'Trading';

    public static function all(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}
