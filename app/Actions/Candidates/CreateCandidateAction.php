<?php

namespace App\Actions\Candidates;

use App\Data\Candidates\CandidateData;
use App\Data\Candidates\PersonData;
use App\Events\CandidateCreated;
use App\Models\Candidate;
use App\Repositories\CandidateRepository;
use App\Repositories\PersonRepository;

class CreateCandidateAction
{
    public function __construct(
        private PersonRepository $personRepository,
        private CandidateRepository $candidateRepository,
    ) {}

    public function handle(PersonData $personData, CandidateData $candidateData): Candidate
    {
        $person = $this->personRepository->create($personData);

        $candidate = $this->candidateRepository->create(new CandidateData(
            personId: $person->id,
            positionId: $candidateData->positionId,
            pipelineStageId: $candidateData->pipelineStageId,
            source: $candidateData->source,
            appliedAt: $candidateData->appliedAt,
        ));

        CandidateCreated::dispatch($candidate->id, $person->id, $candidateData->positionId);

        return $candidate->load(['person', 'position', 'pipelineStage']);
    }
}
