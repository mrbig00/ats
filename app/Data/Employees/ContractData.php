<?php

namespace App\Data\Employees;

use Carbon\CarbonImmutable;

readonly class ContractData
{
    public function __construct(
        public int $employeeId,
        public string $type,
        public CarbonImmutable $startsAt,
        public ?CarbonImmutable $endsAt,
        public ?string $notes,
    ) {}
}
