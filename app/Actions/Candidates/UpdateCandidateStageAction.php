<?php

namespace App\Actions\Candidates;

use App\Data\Candidates\UpdateCandidateStageData;
use App\Events\CandidateStageChanged;
use App\Models\Candidate;
use App\Repositories\CandidateRepository;

class UpdateCandidateStageAction
{
    public function __construct(
        private CandidateRepository $candidateRepository,
    ) {}

    public function handle(UpdateCandidateStageData $data): Candidate
    {
        $candidate = $this->candidateRepository->find($data->candidateId);
        if ($candidate === null) {
            throw new \InvalidArgumentException('Candidate not found.');
        }

        $previousStageId = $candidate->pipeline_stage_id;
        $updated = $this->candidateRepository->updateStage($candidate, $data->pipelineStageId);

        CandidateStageChanged::dispatch($candidate->id, $previousStageId, $data->pipelineStageId);

        return $updated;
    }
}
