<?php

namespace App\Enums;

use Carbon\CarbonImmutable;
use Filament\Support\Contracts\HasLabel;

enum RecordType: string implements HasLabel
{
    case INITIAL = 'initial';
    case YEARLY = 'yearly';
    case MONTHLY = 'monthly';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getStartDate(CarbonImmutable $date): CarbonImmutable
    {
        return match ($this->value) {
            self::INITIAL->value => $date->startOfDay(),
            self::YEARLY->value => $date->startOfYear(),
            self::MONTHLY->value => $date->startOfMonth(),
        };
    }

    public function getEndDate(CarbonImmutable $date): CarbonImmutable
    {
        return match ($this->value) {
            self::INITIAL->value => $date->endOfDay(),
            self::YEARLY->value => $date->endOfYear(),
            self::MONTHLY->value => $date->endOfMonth(),
        };
    }
}
