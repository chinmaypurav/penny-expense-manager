<?php

namespace App\Policies;

use App\Enums\PanelId;
use App\Models\Income;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IncomePolicy
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

    public function update(User $user, Income $income): bool
    {
        if (PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        return $user->id === $income->user_id;
    }

    public function delete(User $user, Income $income): bool
    {
        if (PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        return $user->id === $income->user_id;
    }

    public function import(User $user): bool
    {
        return PanelId::APP->isCurrentPanel();
    }
}
