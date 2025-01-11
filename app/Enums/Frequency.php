<?php

namespace App\Enums;

use Carbon\Carbon;
use Filament\Support\Contracts\HasLabel;

enum Frequency: string implements HasLabel
{
    case ONCE = 'once';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getRemainingIterations(Carbon $start, Carbon $end, int $iterationsLeft): int
    {
        $diff = match ($this->value) {
            self::DAILY->value => $start->diffInDays($end),
            self::WEEKLY->value => $start->diffInWeeks($end),
            self::MONTHLY->value => $start->diffInMonths($end, true),
            self::QUARTERLY->value => $start->diffInQuarters($end),
            self::YEARLY->value => $start->diffInYears($end),
            self::ONCE->value => 0,
        };

        $diff = floor($diff);

        $diff = $diff + 1; // the first date is inclusive

        return min($iterationsLeft, $diff);
    }
}
