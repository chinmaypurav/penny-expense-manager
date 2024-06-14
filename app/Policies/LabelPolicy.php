<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Label;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LabelPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->role === Role::ADMIN;
    }

    public function create(User $user): bool
    {
        return $user->role === Role::ADMIN;
    }

    public function update(User $user, Label $label): bool
    {
        return $user->role === Role::ADMIN;
    }

    public function delete(User $user, Label $label): bool
    {
        return $user->role === Role::ADMIN;
    }

    public function restore(User $user, Label $label): bool
    {
        return $user->role === Role::ADMIN;
    }

    public function forceDelete(User $user, Label $label): bool
    {
        return $user->role === Role::ADMIN;
    }
}
