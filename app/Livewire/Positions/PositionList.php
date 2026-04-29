<?php

namespace App\Livewire\Positions;

use App\Data\Positions\PositionFilterData;
use App\Actions\Positions\ImportPositionsCsvAction;
use App\Data\Imports\CsvImportFileData;
use App\Repositories\PositionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;

class PositionList extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $statusFilter = 'open';

    public bool $includeArchived = false;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'asc';

    public int $perPage = 15;

    public bool $csvImportOpen = false;

    public mixed $csvFile = null;

    public ?array $csvImportResult = null;

    protected function queryString(): array
    {
        return [
            'statusFilter' => ['as' => 'status', 'except' => 'open'],
            'includeArchived' => ['as' => 'archived', 'except' => false],
            'search' => ['as' => 'q', 'except' => ''],
            'sortField' => ['as' => 'sort', 'except' => 'created_at'],
            'sortDirection' => ['as' => 'dir', 'except' => 'asc'],
        ];
    }

    public function mount(): void
    {
        $this->authorize('viewAny', \App\Models\Position::class);
    }

    /**
     * @return LengthAwarePaginator<\App\Models\Position>
     */
    public function getPositionsProperty(): LengthAwarePaginator
    {
        $filters = new PositionFilterData(
            status: $this->statusFilter !== '' ? $this->statusFilter : null,
            search: trim($this->search) !== '' ? trim($this->search) : null,
            sortField: $this->sortField,
            sortDirection: $this->sortDirection,
            perPage: $this->perPage,
            includeArchived: $this->includeArchived,
        );

        return app(PositionRepository::class)->paginate($filters);
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedIncludeArchived(bool $value): void
    {
        $this->resetPage();
        if (! $value) {
            $this->statusFilter = 'open';
        }
    }

    public function updatedSearch(): void
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

    public function openCsvImport(): void
    {
        $this->authorize('importCsv', \App\Models\Position::class);
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
        $this->authorize('importCsv', \App\Models\Position::class);

        $this->validate([
            'csvFile' => ['required', 'file', 'max:51200', 'mimes:csv,txt'],
        ], [], [
            'csvFile' => __('common.csv'),
        ]);

        $path = $this->csvFile->store('csv-imports/positions', 'local');
        $result = app(ImportPositionsCsvAction::class)->handle(new CsvImportFileData(
            path: $path,
            originalName: $this->csvFile->getClientOriginalName(),
        ));

        $this->csvImportResult = $result->toArray();
        $this->reset(['csvFile']);
        $this->dispatch('notify', __('common.csv_import_completed'));
    }

    public function render()
    {
        return view('livewire.positions.position-list', [
            'positions' => $this->positions,
        ])->title(__('nav.jobs'));
    }
}
