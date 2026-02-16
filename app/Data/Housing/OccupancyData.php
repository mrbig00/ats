<?php

namespace App\Data\Housing;

use Carbon\CarbonImmutable;

readonly class OccupancyData
{
    public function __construct(
        public int $roomId,
        public int $employeeId,
        public CarbonImmutable $startsAt,
        public ?CarbonImmutable $endsAt,
    ) {}
}
