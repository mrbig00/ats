<?php

namespace App\Livewire\Candidates;

use App\Data\Candidates\CandidateFilterData;
use App\Data\Imports\CsvImportFileData;
use App\Data\Imports\ImportResultData;
use App\Actions\Candidates\ImportCandidatesCsvAction;
use App\Repositories\CandidateRepository;
use App\Repositories\PipelineStageRepository;
use App\Repositories\PositionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;

class CandidateList extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';

    public bool $includeArchived = false;

    public ?int $pipelineStageId = null;

    public ?int $positionId = null;

    public string $appliedFrom = '';

    public string $appliedTo = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'asc';

    public int $perPage = 15;

    public bool $csvImportOpen = false;

    public mixed $csvFile = null;

    public ?array $csvImportResult = null;

    protected function queryString(): array
    {
        return [
            'search' => ['as' => 'q', 'except' => ''],
            'includeArchived' => ['as' => 'archived', 'except' => false],
            'pipelineStageId' => ['as' => 'stage', 'except' => ''],
            'positionId' => ['as' => 'position', 'except' => ''],
            'appliedFrom' => ['as' => 'from', 'except' => ''],
            'appliedTo' => ['as' => 'to', 'except' => ''],
            'sortField' => ['as' => 'sort', 'except' => 'created_at'],
            'sortDirection' => ['as' => 'dir', 'except' => 'asc'],
        ];
    }

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
            appliedFrom: trim($this->appliedFrom) !== '' ? trim($this->appliedFrom) : null,
            appliedTo: trim($this->appliedTo) !== '' ? trim($this->appliedTo) : null,
            sortField: $this->sortField,
            sortDirection: $this->sortDirection,
            perPage: $this->perPage,
            includeArchived: $this->includeArchived,
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
        return $this->includeArchived
            ? app(PositionRepository::class)->all()
            : app(PositionRepository::class)->allActiveRecruitmentSessions();
    }

    public function updatedIncludeArchived(): void
    {
        $this->resetPage();
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

    public function updatedAppliedFrom(): void
    {
        $this->resetPage();
    }

    public function updatedAppliedTo(): void
    {
        $this->resetPage();
    }

    public function clearAppliedRange(): void
    {
        $this->appliedFrom = '';
        $this->appliedTo = '';
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

    public function openCsvImport(): void
    {
        $this->authorize('importCsv', \App\Models\Candidate::class);
        $this->csvImportOpen = true;
        $this->csvImportResult = null;
    }

    public function closeCsvImport(): void
    {
        $this->csvImportOpen = false;
        $this->reset(['csvFile']);
    }

    public function importCsv(): void
    {
        $this->authorize('importCsv', \App\Models\Candidate::class);

        $this->validate([
            'csvFile' => ['required', 'file', 'max:51200', 'mimes:csv,txt'],
        ], [], [
            'csvFile' => __('common.csv'),
        ]);

        $path = $this->csvFile->store('csv-imports/candidates', 'local');
        $result = app(ImportCandidatesCsvAction::class)->handle(new CsvImportFileData(
            path: $path,
            originalName: $this->csvFile->getClientOriginalName(),
        ));

        $this->csvImportResult = $result->toArray();
        $this->reset(['csvFile']);
        $this->dispatch('notify', __('common.csv_import_completed'));
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
