<?php

namespace App\Data\Meetings;

use Carbon\CarbonImmutable;

readonly class MeetingData
{
    public function __construct(
        public string $title,
        public CarbonImmutable $startsAt,
        public ?CarbonImmutable $endsAt,
        public ?string $notes,
    ) {}
}
