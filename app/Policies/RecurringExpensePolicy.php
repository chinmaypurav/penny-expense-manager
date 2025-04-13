<?php

namespace App\Policies;

use App\Enums\PanelId;
use App\Models\RecurringExpense;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecurringExpensePolicy
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

    public function update(User $user, RecurringExpense $recurringExpense): bool
    {
        if (PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        return $recurringExpense->user_id === $user->id;
    }

    public function delete(User $user, RecurringExpense $recurringExpense): bool
    {
        if (PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        return $recurringExpense->user_id === $user->id;
    }
}
