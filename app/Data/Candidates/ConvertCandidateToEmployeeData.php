<?php

namespace App\Data\Candidates;

use Carbon\CarbonImmutable;

readonly class ConvertCandidateToEmployeeData
{
    public function __construct(
        public int $candidateId,
        public CarbonImmutable $entryDate,
    ) {}
}
