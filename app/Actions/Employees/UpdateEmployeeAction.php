<?php

namespace App\Actions\Employees;

use App\Data\Employees\UpdateEmployeeData;
use App\Models\Employee;
use App\Repositories\EmployeeRepository;

class UpdateEmployeeAction
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
    ) {}

    public function handle(UpdateEmployeeData $data): Employee
    {
        $employee = $this->employeeRepository->find($data->employeeId);
        if ($employee === null) {
            throw new \InvalidArgumentException('Employee not found.');
        }

        if ($data->entryDate !== null) {
            return $this->employeeRepository->updateEntryDate($employee, $data->entryDate);
        }

        return $employee;
    }
}
