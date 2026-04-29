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
        return $user->role->canEditContent();
    }

    public function update(User $user, Apartment $apartment): bool
    {
        return $user->role->canEditContent();
    }

    public function delete(User $user, Apartment $apartment): bool
    {
        return $user->role->canEditContent();
    }

    public function downloadCsvTemplate(User $user): bool
    {
        return $user->role->isAdmin();
    }

    public function exportCsv(User $user): bool
    {
        return $user->role->isAdmin();
    }

    public function importCsv(User $user): bool
    {
        return $user->role->isAdmin();
    }
}
