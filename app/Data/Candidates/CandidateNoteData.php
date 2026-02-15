<?php

namespace App\Data\Candidates;

readonly class CandidateNoteData
{
    public function __construct(
        public int $candidateId,
        public int $userId,
        public string $content,
    ) {}
}
