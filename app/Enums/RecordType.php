<?php

namespace App\Enums;

enum RecordType: string
{
    case INITIAL = 'initial';
    case YEARLY = 'yearly';
    case MONTHLY = 'monthly';

    public static function all(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}
