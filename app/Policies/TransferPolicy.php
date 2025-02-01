<?php

namespace App\Policies;

use App\Enums\PanelId;
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
        return PanelId::APP->isCurrentPanel();
    }

    public function update(User $user, Transfer $transfer): bool
    {
        if (PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        return $user->id === $transfer->user_id;
    }

    public function delete(User $user, Transfer $transfer): bool
    {
        if (PanelId::FAMILY->isCurrentPanel()) {
            return false;
        }

        return $user->id === $transfer->user_id;
    }

    public function import(User $user): bool
    {
        return PanelId::APP->isCurrentPanel();
    }
}
