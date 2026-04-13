<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Candidates\CreateCandidateAction;
use App\Actions\Candidates\UpdateCandidateProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCandidateRequest;
use App\Http\Requests\Api\V1\UpdateCandidateProfileRequest;
use App\Http\Resources\Api\V1\CandidateResource;
use App\Models\Candidate;

class CandidatesController extends Controller
{
    public function __construct(
        private CreateCandidateAction $createCandidateAction,
        private UpdateCandidateProfileAction $updateCandidateProfileAction,
    ) {}

    public function show(Candidate $candidate): CandidateResource
    {
        $this->authorize('view', $candidate);

        return new CandidateResource($candidate->load('person'));
    }

    public function store(StoreCandidateRequest $request): CandidateResource
    {
        $candidate = $this->createCandidateAction->handle(
            $request->toPersonData(),
            $request->toCandidateData(),
        );

        return new CandidateResource($candidate->load('person'));
    }

    public function update(UpdateCandidateProfileRequest $request, Candidate $candidate): CandidateResource
    {
        $candidate = $this->updateCandidateProfileAction->handle(
            $candidate,
            $request->toUpdateCandidateProfileData(),
        );

        return new CandidateResource($candidate->load('person'));
    }
}
