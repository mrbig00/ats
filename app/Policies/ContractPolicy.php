<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Contract $contract): bool
    {
        return $user->can('view', $contract->employee);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Contract $contract): bool
    {
        return $user->can('update', $contract->employee);
    }

    public function delete(User $user, Contract $contract): bool
    {
        return $user->can('update', $contract->employee);
    }
}
