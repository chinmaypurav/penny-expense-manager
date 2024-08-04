<?php

namespace App\Policies;

use App\Models\AnnualStatement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnnualStatementPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AnnualStatement $annualStatement): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AnnualStatement $annualStatement): bool
    {
        return true;
    }

    public function delete(User $user, AnnualStatement $annualStatement): bool
    {
        return true;
    }

    public function restore(User $user, AnnualStatement $annualStatement): bool
    {
        return true;
    }

    public function forceDelete(User $user, AnnualStatement $annualStatement): bool
    {
        return true;
    }
}
