<?php

namespace App\Enums;

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
}
