<?php

namespace App\Data\Candidates;

use Carbon\CarbonImmutable;

readonly class CandidateData
{
    public function __construct(
        public int $personId,
        public int $positionId,
        public int $pipelineStageId,
        public ?string $source,
        public ?CarbonImmutable $appliedAt,
    ) {}
}
