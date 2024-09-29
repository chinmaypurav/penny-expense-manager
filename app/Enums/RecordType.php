<?php

namespace App\Enums;

use App\Concerns\Enumerable;
use Filament\Support\Contracts\HasLabel;

enum RecordType: string implements HasLabel
{
    use Enumerable;

    case INITIAL = 'initial';
    case YEARLY = 'yearly';
    case MONTHLY = 'monthly';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
