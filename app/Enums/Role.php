<?php

namespace App\Enums;

use App\Concerns\Enumerable;

enum Role: string
{
    use Enumerable;

    case ADMIN = 'admin';
    case SPECTATOR = 'spectator';
    case MEMBER = 'member';
}
