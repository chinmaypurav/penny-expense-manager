<?php

namespace App\Policies;

use App\Models\RecurringIncome;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecurringIncomePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, RecurringIncome $recurringIncome): bool
    {
        return $recurringIncome->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, RecurringIncome $recurringIncome): bool
    {
        return $recurringIncome->user_id === $user->id;
    }

    public function delete(User $user, RecurringIncome $recurringIncome): bool
    {
        return $recurringIncome->user_id === $user->id;
    }
}
