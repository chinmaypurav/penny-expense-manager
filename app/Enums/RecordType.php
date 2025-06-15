<?php

namespace App\Enums;

use Carbon\CarbonInterface as Carbon;
use Filament\Support\Contracts\HasLabel;

enum RecordType: string implements HasLabel
{
    case YEARLY = 'yearly';
    case MONTHLY = 'monthly';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getStartDate(Carbon $date): Carbon
    {
        return match ($this->value) {
            self::YEARLY->value => $date->startOfYear(),
            self::MONTHLY->value => $date->startOfMonth(),
        };
    }

    public function getEndDate(Carbon $date): Carbon
    {
        return match ($this->value) {
            self::YEARLY->value => $date->endOfYear(),
            self::MONTHLY->value => $date->endOfMonth(),
        };
    }
}
