<?php

namespace App\Policies;

use App\Enums\PanelId;
use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
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

    public function update(User $user, Account $account): bool
    {
        if (PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        return $user->id === $account->user_id;
    }

    public function delete(User $user, Account $account): bool
    {
        if (PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        return $user->id === $account->user_id;
    }

    public function import(User $user): bool
    {
        return PanelId::APP->isCurrentPanel();
    }
}
