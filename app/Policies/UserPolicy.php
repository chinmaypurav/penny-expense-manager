<?php

namespace App\Policies;

use App\Enums\PanelId;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if (! PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        return $user->id === 1;
    }

    public function create(User $user): bool
    {
        if (! PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        return $user->id === 1;
    }

    public function update(User $user): bool
    {
        if (! PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        return $user->id === 1;
    }

    public function delete(User $user, User $model): bool
    {
        if (! PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        if ($user->id === $model->id) {
            return false;
        }

        return $user->id === 1;
    }
}
