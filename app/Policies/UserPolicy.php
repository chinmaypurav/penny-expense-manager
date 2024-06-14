<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->role === Role::ADMIN;
    }

    public function view(User $user, User $model): bool
    {
        return $user->role === Role::ADMIN;
    }

    public function create(User $user): bool
    {
        return $user->role === Role::ADMIN;
    }

    public function update(User $user, User $model): bool
    {
        return $user->role === Role::ADMIN;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->is($model)) {
            return false;
        }

        return $user->role === Role::ADMIN;
    }

    public function restore(User $user, User $model): bool
    {
        return $user->role === Role::ADMIN;
    }
}
