<?php

namespace App\Policies;

use App\Models\RecurringTransfer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecurringTransferPolicy
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

    public function update(User $user, RecurringTransfer $recurringTransfer): bool
    {
        return $user->id === $recurringTransfer->user_id;
    }

    public function delete(User $user, RecurringTransfer $recurringTransfer): bool
    {
        return $user->id === $recurringTransfer->user_id;
    }
}
