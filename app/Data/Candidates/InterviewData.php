<?php

namespace App\Data\Candidates;

use Carbon\CarbonImmutable;

readonly class InterviewData
{
    public function __construct(
        public int $candidateId,
        public string $title,
        public CarbonImmutable $startsAt,
        public ?CarbonImmutable $endsAt,
        public ?string $notes,
    ) {}
}
