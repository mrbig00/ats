<?php

namespace App\Actions\Candidates;

use App\Data\Candidates\CandidateNoteData;
use App\Models\CandidateNote;
use App\Repositories\CandidateNoteRepository;

class AddCandidateNoteAction
{
    public function __construct(
        private CandidateNoteRepository $candidateNoteRepository,
    ) {}

    public function handle(CandidateNoteData $data): CandidateNote
    {
        return $this->candidateNoteRepository->create($data);
    }
}
