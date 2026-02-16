<?php

namespace App\Data\Employees;

readonly class EmployeeFilterData
{
    public function __construct(
        public ?string $search,
        public ?string $status,
        public string $sortField,
        public string $sortDirection,
        public int $perPage,
    ) {}
}
