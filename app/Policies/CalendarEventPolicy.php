<?php

namespace App\Policies;

use App\Models\CalendarEvent;
use App\Models\User;

class CalendarEventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CalendarEvent $calendarEvent): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role->canEditContent();
    }

    public function update(User $user, CalendarEvent $calendarEvent): bool
    {
        return $user->role->canEditContent();
    }

    public function delete(User $user, CalendarEvent $calendarEvent): bool
    {
        return $user->role->canEditContent();
    }
}
