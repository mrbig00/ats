<?php

namespace App\Data\Calendar;

use Carbon\CarbonImmutable;

readonly class CalendarItemFilterData
{
    /**
     * @param  array<string>|null  $types  Filter by type: meeting, task, event. Null = all.
     */
    public function __construct(
        public CarbonImmutable $start,
        public CarbonImmutable $end,
        public ?array $types,
    ) {}
}
