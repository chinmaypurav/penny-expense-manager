<?php

namespace App\Concerns;

trait Enumerable
{
    public static function all(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}
