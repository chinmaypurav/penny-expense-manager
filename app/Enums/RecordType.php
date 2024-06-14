<?php

namespace App\Enums;

use App\Concerns\Enumerable;

enum RecordType: string
{
    use Enumerable;

    case INITIAL = 'initial';
    case YEARLY = 'yearly';
    case MONTHLY = 'monthly';
}
