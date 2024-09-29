<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Frequency: string implements HasLabel
{
    case ONCE = 'once';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';
    case FOREVER = 'forever';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public static function getRemainingRecurrences(string $frequency, ?int $remainingRecurrences): ?int
    {
        if ($frequency === self::ONCE->value) {
            return 1;
        }
        if ($frequency === self::FOREVER->value) {
            return null;
        }

        return $remainingRecurrences;
    }

    public static function canUpdateRemainingRecurrences(?string $frequency): bool
    {
        return ! in_array($frequency, [
            Frequency::ONCE->value,
            Frequency::FOREVER->value,
        ]);
    }
}
