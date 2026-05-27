<?php

namespace App\Actions\Candidates;

use App\Models\Candidate;

class DeleteCandidateDocumentAction
{
    public function handle(Candidate $candidate, int $mediaId): void
    {
        $media = $candidate->getMedia('documents')->firstWhere('id', $mediaId);

        if ($media === null) {
            return;
        }

        $media->delete();
    }
}
