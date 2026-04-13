<?php

namespace App\Data\Calendar;

use Carbon\CarbonImmutable;

readonly class CalendarItemOccurrenceData
{
    /**
     * Composite id: {calendar_item_id}:{occurrence_start_iso}
     */
    public function __construct(
        public string $id,
        public string $title,
        public CarbonImmutable $start,
        public ?CarbonImmutable $end,
        public bool $allDay,
        public string $type,
        public int $seriesId,
        public bool $isRecurring,
        public string $occurrenceDate,
        public array $extendedProps,
    ) {}
}
