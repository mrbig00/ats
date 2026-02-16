<?php

namespace App\Livewire\Positions;

use App\Data\Positions\PositionFilterData;
use App\Repositories\PositionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class PositionList extends Component
{
    use WithPagination;

    public string $statusFilter = 'open';

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'asc';

    public int $perPage = 15;

    protected function queryString(): array
    {
        return [
            'statusFilter' => ['as' => 'status', 'except' => 'open'],
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
        );

        return app(PositionRepository::class)->paginate($filters);
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
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

    public function render()
    {
        return view('livewire.positions.position-list', [
            'positions' => $this->positions,
        ])->title(__('nav.jobs'));
    }
}
