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
        return $user->role->canEditContent();
    }

    public function update(User $user, Position $position): bool
    {
        return $user->role->canEditContent();
    }

    public function delete(User $user, Position $position): bool
    {
        return $user->role->canEditContent();
    }

    public function edit(User $user, Position $position): bool
    {
        return $user->role->canEditContent() && $position->isOpen();
    }
}
