<?php

namespace App\Policies;

use App\Models\Apartment;
use App\Models\User;

class ApartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Apartment $apartment): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Apartment $apartment): bool
    {
        return true;
    }

    public function delete(User $user, Apartment $apartment): bool
    {
        return true;
    }
}
