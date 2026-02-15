<?php

namespace App\Livewire\Candidates;

use App\Data\Candidates\CandidateFilterData;
use App\Repositories\CandidateRepository;
use App\Repositories\PipelineStageRepository;
use App\Repositories\PositionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class CandidateList extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $pipelineStageId = null;

    public ?int $positionId = null;

    public string $sortField = 'created_at';

    public string $sortDirection = 'asc';

    public int $perPage = 15;

    public function mount(): void
    {
        $this->authorize('viewAny', \App\Models\Candidate::class);
    }

    /**
     * @return LengthAwarePaginator<\App\Models\Candidate>
     */
    public function getCandidatesProperty(): LengthAwarePaginator
    {
        $filters = new CandidateFilterData(
            search: trim($this->search) !== '' ? trim($this->search) : null,
            pipelineStageId: $this->pipelineStageId ? (int) $this->pipelineStageId : null,
            positionId: $this->positionId ? (int) $this->positionId : null,
            sortField: $this->sortField,
            sortDirection: $this->sortDirection,
            perPage: $this->perPage,
        );

        return app(CandidateRepository::class)->paginate($filters);
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
        return app(PositionRepository::class)->all();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPipelineStageId(): void
    {
        $this->resetPage();
    }

    public function updatedPositionId(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.candidates.candidate-list', [
            'candidates' => $this->candidates,
            'pipelineStages' => $this->pipelineStages,
            'positions' => $this->positions,
        ])->title(__('nav.candidates'));
    }
}
