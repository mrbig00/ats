<?php

namespace App\Livewire\Todo;

use App\Actions\Task\DeleteTaskAction;
use App\Data\Task\TaskFilterData;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $priorityFilter = '';

    public string $dueDateFrom = '';

    public string $dueDateTo = '';

    public string $completedFilter = '';

    public string $sortField = 'due_date';

    public string $sortDirection = 'asc';

    public int $perPage = 15;

    protected function queryString(): array
    {
        return [
            'search' => ['as' => 'q', 'except' => ''],
            'priorityFilter' => ['as' => 'priority', 'except' => ''],
            'dueDateFrom' => ['as' => 'from', 'except' => ''],
            'dueDateTo' => ['as' => 'to', 'except' => ''],
            'completedFilter' => ['as' => 'completed', 'except' => ''],
            'sortField' => ['as' => 'sort', 'except' => 'due_date'],
            'sortDirection' => ['as' => 'dir', 'except' => 'asc'],
        ];
    }

    public function mount(): void
    {
        $this->authorize('viewAny', \App\Models\Task::class);
    }

    /**
     * @return LengthAwarePaginator<\App\Models\Task>
     */
    public function getTasksProperty(): LengthAwarePaginator
    {
        $filters = new TaskFilterData(
            userId: auth()->id(),
            search: trim($this->search) !== '' ? trim($this->search) : null,
            priority: $this->priorityFilter !== '' ? $this->priorityFilter : null,
            dueDateFrom: $this->dueDateFrom !== '' ? CarbonImmutable::parse($this->dueDateFrom) : null,
            dueDateTo: $this->dueDateTo !== '' ? CarbonImmutable::parse($this->dueDateTo) : null,
            completed: $this->completedFilter === '1' ? true : ($this->completedFilter === '0' ? false : null),
            sortField: $this->sortField,
            sortDirection: $this->sortDirection,
            perPage: $this->perPage,
        );

        return app(TaskRepository::class)->paginateForUser($filters);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDueDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDueDateTo(): void
    {
        $this->resetPage();
    }

    public function updatedCompletedFilter(): void
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

    public function deleteTask(int $id): void
    {
        $task = Task::query()->findOrFail($id);
        $this->authorize('delete', $task);
        app(DeleteTaskAction::class)->handle($task);
        $this->dispatch('notify', __('task.deleted'));
    }

    public function render()
    {
        return view('livewire.todo.todo-list', [
            'tasks' => $this->tasks,
        ])->title(__('nav.todo'));
    }
}
