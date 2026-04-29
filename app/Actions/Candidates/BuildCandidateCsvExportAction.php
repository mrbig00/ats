<?php

namespace App\Actions\Candidates;

use App\Data\Candidates\CandidateFilterData;
use App\Exports\CandidateExport;
use App\Repositories\CandidateRepository;

class BuildCandidateCsvExportAction
{
    public function handle(CandidateFilterData $filters): CandidateExport
    {
        $query = app(CandidateRepository::class)->exportQuery($filters);

        return new CandidateExport($query);
    }
}

