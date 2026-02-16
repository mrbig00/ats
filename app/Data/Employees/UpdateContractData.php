<?php

namespace App\Data\Employees;

use Carbon\CarbonImmutable;

readonly class UpdateContractData
{
    public function __construct(
        public int $contractId,
        public string $type,
        public CarbonImmutable $startsAt,
        public ?CarbonImmutable $endsAt,
        public ?string $notes,
    ) {}
}
