<?php

namespace App\Livewire\Archive;

use App\Actions\Positions\ReopenPositionAction;
use App\Data\Archive\ArchiveListFilterData;
use App\Models\Position;
use App\Repositories\CandidateRepository;
use App\Repositories\PositionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class ArchiveIndex extends Component
{
    use WithPagination;

    public string $panel = 'jobs';

    public string $jobSearch = '';

    public string $candidateSearch = '';

    public string $jobsSortField = 'closes_at';

    public string $jobsSortDirection = 'desc';

    public string $candidatesSortField = 'created_at';

    public string $candidatesSortDirection = 'desc';

    public int $perPage = 15;

    public function mount(): void
    {
        $this->authorize('viewAny', Position::class);
        $this->authorize('viewAny', \App\Models\Candidate::class);
    }

    public function updatedPanel(): void
    {
        $this->resetPage();
    }

    public function updatedJobSearch(): void
    {
        if ($this->panel === 'jobs') {
            $this->resetPage();
        }
    }

    public function updatedCandidateSearch(): void
    {
        if ($this->panel === 'candidates') {
            $this->resetPage();
        }
    }

    public function sortJobsBy(string $field): void
    {
        if ($this->jobsSortField === $field) {
            $this->jobsSortDirection = $this->jobsSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->jobsSortField = $field;
            $this->jobsSortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function sortCandidatesBy(string $field): void
    {
        if ($this->candidatesSortField === $field) {
            $this->candidatesSortDirection = $this->candidatesSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->candidatesSortField = $field;
            $this->candidatesSortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function reopenPosition(int $positionId): void
    {
        $position = Position::query()->findOrFail($positionId);
        $this->authorize('reopen', $position);
        app(ReopenPositionAction::class)->handle($position);
        $this->dispatch('notify', __('archive.position_reopened'));
        $this->resetPage();
    }

    public function render()
    {
        if ($this->panel === 'jobs') {
            $filters = new ArchiveListFilterData(
                search: trim($this->jobSearch) !== '' ? trim($this->jobSearch) : null,
                sortField: $this->jobsSortField,
                sortDirection: $this->jobsSortDirection,
                perPage: $this->perPage,
                pageName: 'page',
            );
            /** @var LengthAwarePaginator<\App\Models\Position> $paginator */
            $paginator = app(PositionRepository::class)->paginateExpiredSessions($filters);
        } else {
            $filters = new ArchiveListFilterData(
                search: trim($this->candidateSearch) !== '' ? trim($this->candidateSearch) : null,
                sortField: $this->candidatesSortField,
                sortDirection: $this->candidatesSortDirection,
                perPage: $this->perPage,
                pageName: 'page',
            );
            /** @var LengthAwarePaginator<\App\Models\Candidate> $paginator */
            $paginator = app(CandidateRepository::class)->paginateArchivedPipelineApplications($filters);
        }

        return view('livewire.archive.archive-index', [
            'paginator' => $paginator,
        ])->title(__('nav.archive'));
    }
}
