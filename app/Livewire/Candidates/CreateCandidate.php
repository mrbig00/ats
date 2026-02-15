<?php

namespace App\Livewire\Candidates;

use App\Actions\Candidates\CreateCandidateAction;
use App\Data\Candidates\CandidateData;
use App\Data\Candidates\PersonData;
use App\Repositories\PipelineStageRepository;
use App\Repositories\PositionRepository;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class CreateCandidate extends Component
{
    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $phone = '';

    public ?int $positionId = null;

    public ?int $pipelineStageId = null;

    public string $source = '';

    public ?string $appliedAt = null;

    public function mount(): void
    {
        $this->authorize('create', \App\Models\Candidate::class);
        $stages = app(PipelineStageRepository::class)->allOrdered();
        $firstStage = $stages->first();
        if ($firstStage) {
            $this->pipelineStageId = $firstStage->id;
        }
    }

    /**
     * @return Collection<int, \App\Models\PipelineStage>
     */
    public function getPipelineStagesProperty(): Collection
    {
        return app(PipelineStageRepository::class)->allOrdered();
    }

    /**
     * @return Collection<int, \App\Models\Position>
     */
    public function getPositionsProperty(): Collection
    {
        return app(PositionRepository::class)->allOpen();
    }

    public function save(): mixed
    {
        $validated = $this->validate([
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'positionId' => ['required', 'integer', 'exists:positions,id'],
            'pipelineStageId' => ['required', 'integer', 'exists:pipeline_stages,id'],
            'source' => ['nullable', 'string', 'max:100'],
            'appliedAt' => ['nullable', 'date'],
        ], [], [
            'firstName' => __('candidate.first_name'),
            'lastName' => __('candidate.last_name'),
            'email' => __('candidate.email'),
            'phone' => __('candidate.phone'),
            'positionId' => __('candidate.position'),
            'pipelineStageId' => __('candidate.stage'),
            'source' => __('candidate.source'),
            'appliedAt' => __('candidate.applied_at'),
        ]);

        $personData = new PersonData(
            firstName: $validated['firstName'],
            lastName: $validated['lastName'],
            email: $validated['email'] ?: null,
            phone: $validated['phone'] ?: null,
        );

        $candidateData = new CandidateData(
            personId: 0,
            positionId: $validated['positionId'],
            pipelineStageId: $validated['pipelineStageId'],
            source: $validated['source'] ?: null,
            appliedAt: isset($validated['appliedAt']) ? CarbonImmutable::parse($validated['appliedAt']) : null,
        );

        $candidate = app(CreateCandidateAction::class)->handle($personData, $candidateData);

        $this->dispatch('notify', __('candidate.created'));

        return $this->redirect(route('candidates.show', $candidate), navigate: true);
    }

    public function render()
    {
        return view('livewire.candidates.create-candidate', [
            'pipelineStages' => $this->pipelineStages,
            'positions' => $this->positions,
        ])->title(__('candidate.create'));
    }
}
