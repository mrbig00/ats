<?php

namespace App\Data\Meetings;

readonly class MeetingFilterData
{
    public function __construct(
        public ?string $type,
        public ?string $search,
        public string $sortField,
        public string $sortDirection,
        public int $perPage,
    ) {}
}
