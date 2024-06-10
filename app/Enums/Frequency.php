<?php

namespace App\Enums;

enum Frequency: string
{
    case ONCE = 'once';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';

    public static function all(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}
