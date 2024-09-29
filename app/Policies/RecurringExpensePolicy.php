<?php

namespace App\Policies;

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

    public function view(User $user, RecurringExpense $recurringExpense): bool
    {
        return $recurringExpense->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, RecurringExpense $recurringExpense): bool
    {
        return $recurringExpense->user_id === $user->id;
    }

    public function delete(User $user, RecurringExpense $recurringExpense): bool
    {
        return $recurringExpense->user_id === $user->id;
    }
}
