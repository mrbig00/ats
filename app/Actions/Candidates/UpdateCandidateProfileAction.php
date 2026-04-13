<?php

namespace App\Actions\Candidates;

use App\Data\Candidates\UpdateCandidateProfileData;
use App\Models\Candidate;
use App\Repositories\CandidateRepository;

class UpdateCandidateProfileAction
{
    public function __construct(
        private CandidateRepository $candidateRepository,
    ) {}

    public function handle(Candidate $candidate, UpdateCandidateProfileData $data): Candidate
    {
        if ($data->attributes === []) {
            return $candidate->load('person');
        }

        return $this->candidateRepository->applyProfilePatch($candidate, $data);
    }
}
