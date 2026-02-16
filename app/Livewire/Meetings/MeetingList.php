<?php

namespace App\Livewire\Meetings;

use App\Data\Meetings\MeetingFilterData;
use App\Repositories\CalendarEventRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class MeetingList extends Component
{
    use WithPagination;

    public string $typeFilter = '';

    public string $search = '';

    public string $sortField = 'starts_at';

    public string $sortDirection = 'asc';

    public int $perPage = 15;

    protected function queryString(): array
    {
        return [
            'typeFilter' => ['as' => 'type', 'except' => ''],
            'search' => ['as' => 'q', 'except' => ''],
            'sortField' => ['as' => 'sort', 'except' => 'starts_at'],
            'sortDirection' => ['as' => 'dir', 'except' => 'asc'],
        ];
    }

    public function mount(): void
    {
        $this->authorize('viewAny', \App\Models\CalendarEvent::class);
    }

    /**
     * @return LengthAwarePaginator<\App\Models\CalendarEvent>
     */
    public function getMeetingsProperty(): LengthAwarePaginator
    {
        $filters = new MeetingFilterData(
            type: $this->typeFilter !== '' ? $this->typeFilter : null,
            search: trim($this->search) !== '' ? trim($this->search) : null,
            sortField: $this->sortField,
            sortDirection: $this->sortDirection,
            perPage: $this->perPage,
        );

        return app(CalendarEventRepository::class)->paginateMeetings($filters);
    }

    public function updatedTypeFilter(): void
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
        return view('livewire.meetings.meeting-list', [
            'meetings' => $this->meetings,
        ])->title(__('nav.meetings'));
    }
}
