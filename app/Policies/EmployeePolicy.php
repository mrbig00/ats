<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Employee $employee): bool
    {
        return true;
    }

    public function update(User $user, Employee $employee): bool
    {
        return $employee->status === Employee::STATUS_ACTIVE;
    }

    public function terminate(User $user, Employee $employee): bool
    {
        return $employee->status === Employee::STATUS_ACTIVE;
    }
}
