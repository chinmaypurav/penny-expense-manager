<?php

namespace App\Enums;

use Carbon\CarbonInterface as Carbon;
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

    public function getRemainingIterations(Carbon $start, Carbon $end, ?int $iterationsLeft): int
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

        if (is_null($iterationsLeft)) {
            return $diff;
        }

        return min($iterationsLeft, $diff);
    }

    public function getNextTransactionAt(Carbon $nextTransactionAt): Carbon
    {
        return match ($this->value) {
            self::DAILY->value => $nextTransactionAt->addDay(),
            self::WEEKLY->value => $nextTransactionAt->addWeek(),
            self::MONTHLY->value => $nextTransactionAt->addMonth(),
            self::QUARTERLY->value => $nextTransactionAt->addQuarter(),
            self::YEARLY->value => $nextTransactionAt->addYear(),
            default => $nextTransactionAt,
        };
    }
}
