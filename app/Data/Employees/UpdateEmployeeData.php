<?php

namespace App\Data\Employees;

use Carbon\CarbonImmutable;

readonly class UpdateEmployeeData
{
    public function __construct(
        public int $employeeId,
        public ?CarbonImmutable $entryDate,
    ) {}
}
