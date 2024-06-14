<?php

namespace App\Enums;

use App\Concerns\Enumerable;

enum Frequency: string
{
    use Enumerable;

    case ONCE = 'once';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';
}
