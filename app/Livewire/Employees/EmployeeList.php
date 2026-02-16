<?php

namespace App\Livewire\Employees;

use App\Data\Employees\EmployeeFilterData;
use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'active';

    public string $sortField = 'entry_date';

    public string $sortDirection = 'desc';

    public int $perPage = 15;

    protected function queryString(): array
    {
        return [
            'search' => ['as' => 'q', 'except' => ''],
            'status' => ['as' => 'status', 'except' => 'active'],
            'sortField' => ['as' => 'sort', 'except' => 'entry_date'],
            'sortDirection' => ['as' => 'dir', 'except' => 'desc'],
        ];
    }

    public function mount(): void
    {
        $this->authorize('viewAny', Employee::class);
    }

    /**
     * @return LengthAwarePaginator<\App\Models\Employee>
     */
    public function getEmployeesProperty(): LengthAwarePaginator
    {
        $filters = new EmployeeFilterData(
            search: trim($this->search) !== '' ? trim($this->search) : null,
            status: $this->status !== '' ? $this->status : null,
            sortField: $this->sortField,
            sortDirection: $this->sortDirection,
            perPage: $this->perPage,
        );

        return app(EmployeeRepository::class)->paginate($filters);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = $field === 'name' ? 'asc' : 'desc';
        }
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.employees.employee-list', [
            'employees' => $this->employees,
        ])->title(__('nav.employees'));
    }
}
