<?php

namespace App\Policies;

use App\Models\Occupancy;
use App\Models\User;

class OccupancyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Occupancy $occupancy): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role->canEditContent();
    }

    public function update(User $user, Occupancy $occupancy): bool
    {
        return $user->role->canEditContent();
    }

    public function delete(User $user, Occupancy $occupancy): bool
    {
        return $user->role->canEditContent();
    }
}
