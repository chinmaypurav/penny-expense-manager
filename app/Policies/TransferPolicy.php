<?php

namespace App\Policies;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransferPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Transfer $transfer): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Transfer $transfer): bool
    {
        return true;
    }

    public function delete(User $user, Transfer $transfer): bool
    {
        return true;
    }

    public function restore(User $user, Transfer $transfer): bool
    {
        return true;
    }

    public function forceDelete(User $user, Transfer $transfer): bool
    {
        return true;
    }
}
