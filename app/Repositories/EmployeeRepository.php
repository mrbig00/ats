<?php

namespace App\Repositories;

use App\Models\Employee;

class EmployeeRepository
{
    public function create(int $personId, \Carbon\CarbonImmutable $entryDate): Employee
    {
        return Employee::query()->create([
            'person_id' => $personId,
            'status' => Employee::STATUS_ACTIVE,
            'entry_date' => $entryDate->toDateString(),
            'exit_date' => null,
        ]);
    }

    public function find(int $id): ?Employee
    {
        return Employee::query()->with('person')->find($id);
    }

    public function findByPersonId(int $personId): ?Employee
    {
        return Employee::query()->where('person_id', $personId)->first();
    }
}
