<?php

namespace App\Data\Employees;

use Carbon\CarbonImmutable;

readonly class TerminateEmployeeData
{
    public function __construct(
        public int $employeeId,
        public CarbonImmutable $exitDate,
        public string $status,
    ) {}
}
