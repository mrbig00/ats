<?php

namespace App\Actions\Employees;

use App\Data\Employees\UpdateEmployeeProfileData;
use App\Models\Employee;
use App\Repositories\EmployeeRepository;

class UpdateEmployeeProfileAction
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
    ) {}

    public function handle(Employee $employee, UpdateEmployeeProfileData $data): Employee
    {
        if ($data->attributes === []) {
            return $employee->load('person');
        }

        return $this->employeeRepository->applyProfilePatch($employee, $data);
    }
}
