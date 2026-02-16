<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;

class PositionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Position $position): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Position $position): bool
    {
        return true;
    }

    public function delete(User $user, Position $position): bool
    {
        return true;
    }

    public function edit(User $user, Position $position): bool
    {
        return $position->isOpen();
    }
}
