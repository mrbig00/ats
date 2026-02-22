<?php

namespace App\Repositories;

use App\Data\Task\TaskData;
use App\Data\Task\TaskFilterData;
use App\Models\Task;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    /**
     * Upcoming and overdue tasks for the user, sorted by priority (high first) then due_date.
     *
     * @return Collection<int, Task>
     */
    public function getUpcomingAndOverdueForUser(int $userId, int $limit = 15): Collection
    {
        $today = CarbonImmutable::today();
        $cutoff = $today->addDays(30)->toDateString();

        return Task::query()
            ->where('user_id', $userId)
            ->whereNull('completed_at')
            ->where('due_date', '<=', $cutoff)
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 END")
            ->orderBy('due_date')
            ->limit($limit)
            ->get();
    }

    public function create(TaskData $data): Task
    {
        return Task::query()->create([
            'user_id' => $data->userId,
            'title' => $data->title,
            'priority' => $data->priority,
            'due_date' => $data->dueDate->toDateString(),
            'completed_at' => $data->completedAt?->toDateTimeString(),
        ]);
    }

    public function update(Task $task, TaskData $data): Task
    {
        $task->update([
            'title' => $data->title,
            'priority' => $data->priority,
            'due_date' => $data->dueDate->toDateString(),
            'completed_at' => $data->completedAt?->toDateTimeString(),
        ]);

        return $task->fresh();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    /**
     * Tasks with due_date in range (for dashboard calendar). Incomplete only.
     *
     * @return Collection<int, Task>
     */
    public function getTasksForCalendar(CarbonImmutable $start, CarbonImmutable $end, int $userId): Collection
    {
        return Task::query()
            ->where('user_id', $userId)
            ->whereNull('completed_at')
            ->where('due_date', '>=', $start->toDateString())
            ->where('due_date', '<=', $end->toDateString())
            ->orderBy('due_date')
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 END")
            ->get();
    }

    /**
     * @return LengthAwarePaginator<Task>
     */
    public function paginateForUser(TaskFilterData $filters): LengthAwarePaginator
    {
        $query = Task::query()->where('user_id', $filters->userId);

        if ($filters->search !== null && trim($filters->search) !== '') {
            $search = '%' . addcslashes(trim($filters->search), '%_') . '%';
            $query->where('title', 'ilike', $search);
        }

        if ($filters->priority !== null && $filters->priority !== '') {
            $query->where('priority', $filters->priority);
        }

        if ($filters->dueDateFrom !== null) {
            $query->where('due_date', '>=', $filters->dueDateFrom->toDateString());
        }

        if ($filters->dueDateTo !== null) {
            $query->where('due_date', '<=', $filters->dueDateTo->toDateString());
        }

        if ($filters->completed === true) {
            $query->whereNotNull('completed_at');
        } elseif ($filters->completed === false) {
            $query->whereNull('completed_at');
        }

        $direction = strtolower($filters->sortDirection) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($this->sortFieldColumn($filters->sortField), $direction);

        return $query->paginate($filters->perPage);
    }

    private function sortFieldColumn(string $field): string
    {
        return match ($field) {
            'title' => 'title',
            'priority' => 'priority',
            'due_date' => 'due_date',
            'completed_at' => 'completed_at',
            default => 'due_date',
        };
    }
}
