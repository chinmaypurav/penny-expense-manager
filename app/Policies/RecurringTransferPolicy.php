<?php

namespace App\Policies;

use App\Enums\PanelId;
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
        return PanelId::APP->isCurrentPanel();
    }

    public function update(User $user, RecurringTransfer $recurringTransfer): bool
    {
        if (PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        return $user->id === $recurringTransfer->user_id;
    }

    public function delete(User $user, RecurringTransfer $recurringTransfer): bool
    {
        if (PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        return $user->id === $recurringTransfer->user_id;
    }
}
