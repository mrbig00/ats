<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CandidateCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $candidateId,
        public int $personId,
        public int $positionId,
    ) {}
}
