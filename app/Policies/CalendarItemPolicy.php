<?php

namespace App\Policies;

use App\Models\CalendarItem;
use App\Models\User;

class CalendarItemPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CalendarItem $calendarItem): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role->canEditContent();
    }

    public function update(User $user, CalendarItem $calendarItem): bool
    {
        return $user->role->canEditContent();
    }

    public function delete(User $user, CalendarItem $calendarItem): bool
    {
        return $user->role->canEditContent();
    }
}
