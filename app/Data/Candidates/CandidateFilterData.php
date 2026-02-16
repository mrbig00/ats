<?php

namespace App\Data\Candidates;

readonly class CandidateFilterData
{
    public function __construct(
        public ?string $search,
        public ?int $pipelineStageId,
        public ?int $positionId,
        public ?string $appliedFrom,
        public ?string $appliedTo,
        public string $sortField,
        public string $sortDirection,
        public int $perPage,
    ) {}
}
