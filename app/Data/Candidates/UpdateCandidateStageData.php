<?php

namespace App\Data\Candidates;

readonly class UpdateCandidateStageData
{
    public function __construct(
        public int $candidateId,
        public int $pipelineStageId,
    ) {}
}
