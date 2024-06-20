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

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Transfer $transfer): bool
    {
        return $user->id === $transfer->user_id;
    }

    public function delete(User $user, Transfer $transfer): bool
    {
        return $user->id === $transfer->user_id;
    }
}
